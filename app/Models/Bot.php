<?php

namespace App\Models;

use Illuminate\Support\Sleep;

class Bot
{
    public function reply(Entry $entry, callable $callback = null)
    {
        $callback ??= fn() => null;

        $answers = [
            'You first intercept the `turbo:submit-start` event and add a custom `text/vnd.chunked-turbo-stream.html` content type to the `Accept` header. Then, we can detect that in the backend, hold the connection by returning a chunked response using the `Transfer-Encoding: chunked` header, and stream many Turbo Streams down the wire. Finally, in the frontend, we can intercept the `turbo:before-fetch-response` event, check if the response has the custom content type of `text/vnd.chunked-turbo-stream.html`, prevent the default behavior of Turbo, and start reading the chunks and render the Turbo Streams.',
            'You\'re welcome!',
        ];

        $answer = explode(' ', $answers[$entry->chat->entries->count() == 2 ? 0 : 1]);

        foreach ($answer as $word) {
            Sleep::for(rand(100, 300))->milliseconds();

            $entry->entryable->update([
                'content' => $entry->entryable->content . ' ' . $word,
            ]);

            $callback($entry);
        }

        $entry->entryable->update([
            'completed_at' => now(),
        ]);

        $callback($entry);
    }
}
