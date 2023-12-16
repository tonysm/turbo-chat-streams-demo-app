import { Controller } from "@hotwired/stimulus"
import { renderStreamMessage } from "@hotwired/turbo"
import { v4 as uuid } from "uuid"

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
        fetchRequest.fetchOptions.headers['X-Turbo-Stream-Chunk-Id'] = uuid()
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

                let streams = decoder.decode(chunk)

                try {
                    streams && callback(JSON.parse(streams))
                } catch (error) {
                    console.log(streams)
                    // Do nothing...
                }

                if (done) break
            }

            this.#removeFinishedRequest(response.headers.get('X-Turbo-Stream-Chunk-Id'))
        } catch (error) {
            if (error?.name != "AbortError") {
                console.error('Error processing chunks', error)
            }
        } finally {
            reader.releaseLock()
        }
    }

    #removeFinishedRequest(requestId) {
        this.#requests = this.#requests.filter(req => req.fetchOptions.headers['X-Turbo-Stream-Chunk-Id'] != requestId)
    }
}

function fetchResponseFromEvent(event) {
    return event.detail?.fetchResponse?.response
}

function fetchResponseIsChunkedTurboStreams({ headers }) {
    return headers.get('Content-Type').includes(TurboStreamChunkedMessage.contentType)
}
