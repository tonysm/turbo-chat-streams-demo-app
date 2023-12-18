<?php

namespace App\Models\Entry;

use App\Jobs\GenerateReply;
use App\Models\Bot;

trait AutoReplies
{
    public static function bootAutoReplies()
    {
        static::created(function ($entry) {
            // $entry->autoGenerateReplyLater();
        });
    }

    public function autoGenerateReplyLater(): void
    {
        if (! $this->shouldAutoGenerate()) {
            return;
        }

        $this->generateLater();
    }

    public function generateLater(): void
    {
        GenerateReply::dispatch($this);
    }

    public function generate(Bot $bot, callable $callback = null): void
    {
        $bot->reply($this, $callback);
    }

    public function shouldAutoGenerate(): bool
    {
        return method_exists($this->entryable, 'shouldAutoGenerate') && $this->entryable->shouldAutoGenerate();
    }

    public function isComplete(): bool
    {
        if (! method_exists($this->entryable, 'isComplete')) {
            return true;
        }

        return $this->entryable->isComplete();
    }
}
