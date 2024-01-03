import { Controller } from "@hotwired/stimulus"
import { renderStreamMessage } from "@hotwired/turbo"

class StreamedTurboStreamMessage {
    static contentType = 'text/vnd.streamed-turbo-stream.html'
}

// Connects to data-controller="streamed-responses"
export default class extends Controller {
    #requests = []

    disconnect() {
        let request;

        while (request = this.#requests.pop()) {
            request.cancel()
        }
    }

    prepareRequest({ detail: { formSubmission: { fetchRequest } } }) {
        if (fetchRequest.fetchOptions.headers.Accept.includes(StreamedTurboStreamMessage.contentType)) return

        fetchRequest.fetchOptions.headers.Accept = `${StreamedTurboStreamMessage.contentType}, ${fetchRequest.fetchOptions.headers.Accept}`
        this.#requests.push(fetchRequest)
    }

    inspectFetchResponse(event) {
        const response = fetchResponseFromEvent(event)

        if (response && fetchResponseIsStream(response)) {
            event.preventDefault()

            this.#startReadingStream(response, (stream) => {
                renderStreamMessage(stream)
            })
        }
    }

    async #startReadingStream(response, callback) {
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

function fetchResponseIsStream({ headers }) {
    return headers.get('Content-Type').includes(StreamedTurboStreamMessage.contentType)
}
