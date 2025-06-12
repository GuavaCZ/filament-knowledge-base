<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

trait CanDisableSyntaxHighlighting
{
    protected bool $disableSyntaxHighlighting = false;

    public function disableSyntaxHighlighting(bool $condition = true): static
    {
        $this->disableSyntaxHighlighting = $condition;

        return $this;
    }

    public function shouldDisableSyntaxHighlighting(): bool
    {
        return $this->evaluate($this->disableSyntaxHighlighting);
    }
}
