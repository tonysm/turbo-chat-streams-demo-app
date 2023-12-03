<?php

namespace App\Providers;

use HotwiredLaravel\TurboLaravel\Http\MultiplePendingTurboStreamResponse;
use HotwiredLaravel\TurboLaravel\Http\PendingTurboStreamResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Request::macro('wantsTurboStreamEventStream', function () {
            return str_contains($this->header('Accept', ''), 'text/event-stream');
        });

        Response::macro('turboStreamsEventStream', function ($callback) {
            $send = function (MultiplePendingTurboStreamResponse|PendingTurboStreamResponse $streams) {
                echo json_encode([
                    'stream' => true,
                    'body' => (string) $streams,
                    'endStream' => true,
                ]);

                echo PHP_EOL;

                if (ob_get_level() > 0) {
                    ob_flush();
                }

                flush();
            };

            $response = response()->stream(null, 200, [
                'Content-Type' => 'text/event-stream',
                'X-Accel-Buffering' => 'no',
                'Cach-Control' => 'no-cache',
                'X-Turbo-Stream' => 'yes',
            ]);

            $response->sendHeaders();

            $callback($send);
        });
    }
}
