<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

trait HasAnchorSymbol
{
    protected ?string $anchorSymbol = '#';

    public function anchorSymbol(?string $symbol): static
    {
        $this->anchorSymbol = $symbol;

        return $this;
    }

    public function getAnchorSymbol(): ?string
    {
        return $this->evaluate($this->anchorSymbol);
    }

    public function disableAnchors(): static
    {
        $this->anchorSymbol = null;

        return $this;
    }
}
