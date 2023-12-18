<?php

namespace App\Models;

use App\Models\Entry\Message;
use App\Models\Entry\Reply;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function createMessageWithReply($attributes)
    {
        $message = $this->entries()->create([
            'entryable' => Message::create($attributes),
        ]);

        $reply = $this->entries()->create([
            'entryable' => Reply::create(),
        ]);

        return [$message, $reply];
    }
}
