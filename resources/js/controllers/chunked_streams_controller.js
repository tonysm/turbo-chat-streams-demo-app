import { Controller } from "@hotwired/stimulus"
import { renderStreamMessage } from "@hotwired/turbo"

class TurboStreamChunkedMessage {
    static contentType = 'text/vnd.chunked-turbo-stream.html'
}

// Connects to data-controller="chunked-streams"
export default class extends Controller {
    prepareRequest({ detail: { formSubmission: { fetchRequest: { fetchOptions: { headers }}}}}) {
        if (headers.Accept.includes(TurboStreamChunkedMessage.contentType)) return

        headers.Accept = `${TurboStreamChunkedMessage.contentType}, ${headers.Accept}`
    }

    inspectFetchResponse(event) {
        const response = fetchResponseFromEvent(event)

        if (response && fetchResponseIsEventSource(response)) {
            event.preventDefault()

            this.#startReceivingChunks(response, (stream) => {
                renderStreamMessage(stream)
            })
        }
    }

    async #startReceivingChunks(response, callback) {
        const reader = response.body.getReader()
        const decoder = new TextDecoder('utf-8')

        try {
            while (this.element.isConnected) {
                let { done, value: chunk } = await reader.read()

                let [hex, streams] = decoder.decode(chunk).split(/\r?\n/, 2)

                let length = parseInt(hex, 16)

                if (length > 0) {
                    callback(JSON.parse(streams.substring(0, length)))
                }

                if (done) break
            }
        } catch (error) {
            console.error('Error processing chunks:', error)
        } finally {
            reader.releaseLock()
        }
    }
}

function fetchResponseFromEvent(event) {
    return event.detail?.fetchResponse?.response
}

function fetchResponseIsEventSource({ headers }) {
    return headers.get('Transfer-Encoding').includes('chunked')
        && headers.has('X-Turbo-Stream-Chunked')
        && headers.get('Content-Type').includes(TurboStreamChunkedMessage.contentType)
}
