<?php

namespace Guava\FilamentKnowledgeBase\Contracts;

interface Documentable
{
    public function getId(): string;

    public function getTitle(): ?string;

    public function isRegistered(): bool;

    public function getContent(): string;

    public function getParent(): ?string;

    public function getGroup(): ?string;

    public function getOrder(): int;

    public function getIcon(): ?string;

    public function getBreadcrumbs(): array;
}
