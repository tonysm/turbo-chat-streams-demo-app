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
        [$message, $reply] = $chat->createMessageWithReply(request()->validate([
            'content' => ['required'],
        ]));

        if (request()->wantsTurboStreamChunks()) {
            return response()->turboStreamsChunks(function ($stream) use ($chat, $message, $reply, $bot) {
                $stream((string) turbo_stream([
                    turbo_stream()->append(dom_id($chat, 'entries'))->view('entries._entry', ['entry' => $message]),
                    turbo_stream()->append(dom_id($chat, 'entries'))->view('entries._entry', ['entry' => $reply]),
                    turbo_stream()->update(dom_id($chat, 'create_message'), view('chat-messages.partials.message-form', ['chat' => $chat])),
                ]));

                $reply->generate($bot, function ($reply) use ($stream) {
                    $stream((string) turbo_stream()->target(dom_id($reply))->action('morph')->view('entries._entry', ['entry' => $reply]));
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
