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
            return str_contains($this->header('Accept', ''), 'text/vnd.turbo-stream-chunked.html');
        });

        Response::macro('turboStreamsEventStream', function ($callback) {
            $send = function (string $chunk) {
                if (connection_aborted()) return;

                // Making sure this is all in one line...
                $chunk = json_encode($chunk);

                echo dechex(strlen($chunk)) . PHP_EOL;
                echo $chunk . PHP_EOL;

                if (ob_get_level() > 0) {
                    ob_flush();
                }

                flush();
            };

            $response = response()->stream(null, 200, [
                'Content-Type' => 'text/vnd.turbo-stream-chunked.html',
                'Transfer-Encoding' => 'chunked',
                'X-Accel-Buffering' => 'no',
                'Cache-Control' => 'no-cache',
                'X-Turbo-Stream-Chunked' => 'yes',
            ]);

            $response->sendHeaders();

            $callback($send);

            echo "0" . PHP_EOL;
            echo PHP_EOL;
        });
    }
}
