<?php

namespace Guava\FilamentKnowledgeBase\Filament\Navigation;

use Filament\Panel;
use Guava\FilamentKnowledgeBase\Enums\NodeType;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Guava\FilamentKnowledgeBase\Models\FlatfileNode;

class Navigation
{
    public function __construct(
        protected Panel $panel
    ) {
    }

    public function build(): void
    {
        $this->panel->navigationGroups(
            KnowledgeBase::model()::query()
                ->where('panel_id', $this->panel->getId())
                ->type(NodeType::Group)
                ->get()
                ->sort(fn (FlatfileNode $d1, FlatfileNode $d2) => $d1->order <=> $d2->order)
                ->map(fn (FlatfileNode $node) => $node->toNavigationGroup())
                ->all()
        );

        $this->panel->navigationItems(
            KnowledgeBase::model()::query()
                ->where('panel_id', $this->panel->getId())
                ->type(NodeType::Documentation, NodeType::Link)
                ->get()
                ->sort(fn (FlatfileNode $d1, FlatfileNode $d2) => $d1->order <=> $d2->order)
                ->map(fn (FlatfileNode $node) => $node->toNavigationItem())
                ->all()
        );
    }

    public static function make(Panel $panel): static
    {
        return app(static::class, [
            'panel' => $panel,
        ]);
    }
}
