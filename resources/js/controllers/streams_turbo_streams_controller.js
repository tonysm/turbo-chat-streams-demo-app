import { Controller } from "@hotwired/stimulus"
import { renderStreamMessage } from "@hotwired/turbo"

class TurboStreamChunkedMessage {
    static contentType = 'text/vnd.turbo-stream-chunked.html'
}

// Connects to data-controller="streams-turbo-streams"
export default class extends Controller {
    prepareRequest({ detail: { fetchOptions: { headers } } }) {
        if (headers.Accept.includes(TurboStreamChunkedMessage.contentType)) return

        headers.Accept = `${TurboStreamChunkedMessage.contentType}, ${headers.Accept}`
    }

    inspectFetchResponse(event) {
        const response = fetchResponseFromEvent(event)

        if (response && fetchResponseIsEventSource(response)) {
            event.preventDefault()

            this.#startReceivingStreams(response, (stream) => {
                renderStreamMessage(stream)
            })
        }
    }

    async #startReceivingStreams(response, callback) {
        const reader = response.body.getReader()
        let decoder = new TextDecoder()

        while (this.element.isConnected) {
            let { done, value: chunk } = await reader.read()

            let [length, streams] = decoder.decode(chunk).split(/\r?\n/, 2)

            length = parseInt(length, 16)

            if (length > 0) {
                callback(JSON.parse(streams.slice(0, length)))
            }

            if (done) break
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
