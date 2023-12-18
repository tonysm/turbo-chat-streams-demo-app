<?php

namespace App\Models\Entry;

use App\Models\Entry;

trait Entryable
{
    public function entry()
    {
        return $this->morphOne(Entry::class, 'entryable');
    }
}
