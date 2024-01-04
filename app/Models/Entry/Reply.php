<?php

namespace App\Models\Entry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use Entryable;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    protected $touches = [
        'entry',
    ];

    public function shouldAutoGenerate(): bool
    {
        return true;
    }

    public function isComplete(): bool
    {
        return boolval($this->completed_at);
    }
}
