<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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

                    echo json_encode($chunk) . PHP_EOL;

                    if (ob_get_level() > 0) {
                        ob_flush();
                    }

                    flush();
                };

                $callback($stream);
            }, 200, [
                'Content-Type' => 'text/vnd.chunked-turbo-stream.html',
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
            ]);
        });
    }
}
