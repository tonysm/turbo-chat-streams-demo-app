<?php

namespace App\Jobs;

use App\Models\Bot;
use App\Models\Entry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateReply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Entry $entry)
    {
        //
    }

    public function handle(Bot $bot): void
    {
        $this->entry->generate($bot);
    }
}
