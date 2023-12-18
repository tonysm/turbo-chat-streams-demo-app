<?php

namespace App\Models;

use App\Models\Entry\AutoReplies;
use App\Models\Entry\Message;
use App\Models\Entry\Reply;
use HotwiredLaravel\TurboLaravel\Models\Broadcasts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read Message|Reply $entryable
 */
class Entry extends Model
{
    use HasFactory;
    use AutoReplies;
    use Broadcasts;

    protected $guarded = [];

    // protected $broadcastsTo = 'chat';

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function entryable()
    {
        return $this->morphTo();
    }

    public function setEntryableAttribute($entryable)
    {
        $this->entryable()->associate($entryable);
    }
}
