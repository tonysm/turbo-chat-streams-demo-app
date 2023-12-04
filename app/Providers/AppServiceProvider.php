<?php

namespace App\Providers;

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
        Request::macro('wantsTurboStreamChunks', function () {
            return str_contains($this->header('Accept', ''), 'text/vnd.chunked-turbo-stream.html');
        });

        Response::macro('turboStreamsChunks', function ($callback) {
            return response()->stream(function () use ($callback) {
                $stream = function (string $chunk) {
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

                $callback($stream);

                echo "0" . PHP_EOL . PHP_EOL;
            }, 200, [
                'Content-Type' => 'text/vnd.chunked-turbo-stream.html',
                'Transfer-Encoding' => 'chunked',
                'X-Accel-Buffering' => 'no',
                'Cache-Control' => 'no-cache',
                'X-Turbo-Stream-Chunked' => 'yes',
            ]);
        });
    }
}
