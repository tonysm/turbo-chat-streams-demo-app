<?php

namespace App\Models\Entry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use Entryable;
    use HasFactory;

    protected $guarded = [];
}
