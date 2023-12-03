import { Controller } from "@hotwired/stimulus"
import { renderStreamMessage } from "@hotwired/turbo"

class EventStreamMessage {
    static contentType = 'text/vnd.turbo-stream-chunked.html'
}

// Connects to data-controller="streams-turbo-streams"
export default class extends Controller {
    #mounted = false

    connect() {
        this.#mounted = true
    }

    disconnect() {
        this.#mounted = false
    }

    prepareRequest({ detail: { fetchOptions } }) {
        if (fetchOptions.headers.Accept.includes(EventStreamMessage.contentType)) return

        fetchOptions.headers.Accept = `${EventStreamMessage.contentType}, ${fetchOptions.headers.Accept}`
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

    async #startReceivingStreams({ response }, callback) {
        const reader = response.body.getReader()
        let remainingResponse = ''

        while (this.#mounted) {
            let { done, value: chunk } = await reader.read()

            let decoder = new TextDecoder()
            let output = decoder.decode(chunk)

            let [streams, remaining] = extractStreamObjects(remainingResponse + output)

            streams.forEach(stream => {
                callback(stream)
            })

            remainingResponse = remaining

            if (done) break
        }
    }
}

function fetchResponseFromEvent(event) {
    return event.detail?.fetchResponse
}

function fetchResponseIsEventSource(response) {
    const contentType = response.contentType ?? ""

    return contentType.startsWith(EventStreamMessage.contentType) && response.response.headers.has('X-Turbo-Stream')
}

function extractStreamObjects(raw) {
    let PATTERN = /{"stream":true.*"endStream":true}/g
    let matches = raw.match(PATTERN)
    let parsed = []

    if (matches) {
        for (let i = 0; i < matches.length; i++) {
            parsed.push(JSON.parse(matches[i]).body)
        }
    }

    let remaining = raw.replace(PATTERN, '')

    return [parsed, remaining]
}
