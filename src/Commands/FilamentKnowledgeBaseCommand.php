<?php

namespace Guava\FilamentKnowledgeBase\Commands;

use Illuminate\Console\Command;

class FilamentKnowledgeBaseCommand extends Command
{
    public $signature = 'filament-knowledge-base';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
