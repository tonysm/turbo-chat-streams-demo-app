import { Controller } from "@hotwired/stimulus"
import { renderStreamMessage } from "@hotwired/turbo"

class TurboStreamChunkedMessage {
    static contentType = 'text/vnd.chunked-turbo-stream.html'
}

// Connects to data-controller="chunked-streams"
export default class extends Controller {
    #requests = []

    disconnect() {
        let request;

        while (request = this.#requests.pop()) {
            request.cancel()
        }
    }

    prepareRequest({ detail: { formSubmission: { fetchRequest } } }) {
        if (fetchRequest.fetchOptions.headers.Accept.includes(TurboStreamChunkedMessage.contentType)) return

        fetchRequest.fetchOptions.headers.Accept = `${TurboStreamChunkedMessage.contentType}, ${fetchRequest.fetchOptions.headers.Accept}`
        this.#requests.push(fetchRequest)
    }

    inspectFetchResponse(event) {
        const response = fetchResponseFromEvent(event)

        if (response && fetchResponseIsChunkedTurboStreams(response)) {
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

                let streams = decoder.decode(chunk).trim()

                streams && callback(JSON.parse(streams))

                if (done) break
            }
        } catch (error) {
            if (error?.name != "AbortError") {
                console.error('Error processing chunks', error)
            }
        } finally {
            reader.releaseLock()
        }
    }
}

function fetchResponseFromEvent(event) {
    return event.detail?.fetchResponse?.response
}

function fetchResponseIsChunkedTurboStreams({ headers }) {
    return headers.get('Content-Type').includes(TurboStreamChunkedMessage.contentType)
}
