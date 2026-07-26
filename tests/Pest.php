<?php

use Filament\Panel;
use Guava\FilamentKnowledgeBase\KnowledgeBase;
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;
use Guava\FilamentKnowledgeBase\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Binds a bare plugin so the renderer can read its options without a panel.
 */
function fakeKnowledgeBasePlugin(): void
{
    app()->instance(KnowledgeBase::class, new class extends KnowledgeBase
    {
        public function plugin(Panel | string | null $panel = null): KnowledgeBasePlugin
        {
            return KnowledgeBasePlugin::make();
        }
    });
}
