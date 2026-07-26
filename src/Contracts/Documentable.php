<?php

namespace Guava\FilamentKnowledgeBase\Contracts;

use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Guava\FilamentKnowledgeBase\Enums\NodeType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface Documentable
{
    public function getId(): string;

    public function getTitle(): ?string;

    public function isActive(): bool;

    public function getContent(): string;

    public function getOrder(): int;

    public function getIcon(): ?string;

    public function getBreadcrumbs(): array;

    public function getType(): NodeType;

    public function getData(): array;

    public function getPanelId(): string;

    public function getUrl(): string;

    /**
     * @return array<string, string> Map of anchor id => label.
     */
    public function getAnchors(): array;

    /**
     * @return Collection<int, Model&Documentable>
     */
    public function children(): Collection;

    public function toNavigationItem(): NavigationItem;

    public function toNavigationGroup(): NavigationGroup;
}
