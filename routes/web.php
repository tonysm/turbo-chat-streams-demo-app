<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePasswordController;
use App\Models\Bot;
use App\Models\Chat;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Sleep;
use Symfony\Component\HttpFoundation\StreamedResponse;

use function HotwiredLaravel\TurboLaravel\dom_id;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/chats/create', function () {
        return view('chats.create');
    })->name('chats.create');

    Route::post('/chats', function () {
        $chat = Chat::create(request()->validate([
            'name' => ['required'],
        ]));

        return redirect()->route('chats.show', $chat);
    })->name('chats.store');

    Route::get('/chats/{chat}', function (Chat $chat) {
        return view('chats.show', [
            'chat' => $chat,
        ]);
    })->name('chats.show');

    Route::get('/chats/{chat}/messages/create', function (Chat $chat) {
       return view('chat-messages.create', [
            'chat' => $chat,
       ]);
    })->name('chats.messages.create');

    Route::post('/chats/{chat}/messages', function (Chat $chat, Bot $bot) {
        $message = $chat->messages()->create(request()->validate([
            'content' => ['required'],
        ]) + [
            'completed_at' => now(),
        ]);

        $reply = $chat->messages()->create([
            'content' => '',
        ]);

        if (request()->wantsTurboStreamEventStream()) {
            return response()->turboStreamsEventStream(function ($send) use ($bot, $chat, $message, $reply) {
                $send(turbo_stream([
                    turbo_stream()
                        ->action('append')
                        ->target(dom_id($chat, 'messages'))
                        ->view('messages.partials.message', ['message' => $message]),
                    turbo_stream()
                        ->action('append')
                        ->target(dom_id($reply->chat, 'messages'))
                        ->view('messages.partials.message', ['message' => $reply]),
                    turbo_stream()
                        ->action('update')
                        ->target(dom_id($message->chat, 'create_message'))
                        ->view('chat-messages.partials.message-form', ['chat' => $message->chat]),
                ]));

                $bot->reply($reply, function ($reply) use ($send) {
                    if (connection_aborted()) return false;

                    $send(turbo_stream()
                        ->action('replace')
                        ->target(dom_id($reply))
                        ->view('messages.partials.message', ['message' => $reply]));
                });
            });
        }

        return redirect()->route('chats.show', $chat)->with('notice', __('Message created.'));
    })->name('chats.messages.store');

    Route::singleton('profile', ProfileController::class);

    Route::prefix('profile')->as('profile.')->group(function () {
        Route::singleton('password', ProfilePasswordController::class)->only(['edit', 'update']);
    });

    Route::get('/profile/delete', [ProfileController::class, 'delete'])->name('profile.delete');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
