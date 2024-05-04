<?php

namespace Guava\FilamentKnowledgeBase;

use Guava\FilamentKnowledgeBase\Contracts\Documentable;

abstract class Documentation implements Documentable
{
    abstract public function getModel();

    public function getId(): string
    {
        return str(static::class)->after('App\\Docs')
            ->trim('\\')
            ->replace('\\', '.')
            ->lower()
        ;
    }

    public function getOrder(): int
    {
        return 0;
    }

    public function getGroup(): ?string
    {
        return null;
    }

    public function getParent(): ?string
    {
        return null;
    }

    public function getIcon(): ?string
    {
        return null;
    }

    public function isRegistered(): bool
    {
        return true;
    }

    public function __toString(): string
    {
        return $this->getId();
    }
}
