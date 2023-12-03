<?php

namespace App\Models;

use Illuminate\Support\Sleep;

class Bot
{
    public function reply(Message $reply, callable $callback)
    {
        $answers = [
            'You first intercept the `turbo:before-fetch-request` and add the `text/event-stream` content type to the `Accept` header. Then we can detect that in the backend, hold the connection, and return a `text/event-stream` response, sending multiple Turbo Streams down the pipe. Then we can intercept the `turbo:before-fetch-response` and detect that it returned a `text/event-stream` response, prevent the default handling of the event, and finally start reading the response bytes and applying the streams.',
            'You\'re welcome!',
        ];

        $answer = explode(' ', $answers[count($reply->chat->messages) == 2 ? 0 : 1]);

        foreach ($answer as $word) {
            Sleep::for(rand(300, 500))->milliseconds();

            $reply->update([
                'content' => $reply->content . ' ' . $word,
            ]);

            if ($callback($reply) === false) {
                return;
            }
        }

        $callback(tap($reply)->update([
            'completed_at' => now(),
        ]));
    }
}
