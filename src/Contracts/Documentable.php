<?php

namespace Guava\FilamentKnowledgeBase\Contracts;

use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Guava\FilamentKnowledgeBase\Enums\NodeType;

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

    public function toNavigationItem(): NavigationItem;

    public function toNavigationGroup(): NavigationGroup;
}
