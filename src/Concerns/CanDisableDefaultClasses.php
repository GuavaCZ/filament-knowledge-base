<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

trait CanDisableDefaultClasses
{
    protected bool $disableDefaultClasses = false;

    public function disableDefaultClasses(bool $condition = true): static
    {
        $this->disableDefaultClasses = $condition;

        return $this;
    }

    public function shouldDisableDefaultClasses(): bool
    {
        return $this->evaluate($this->disableDefaultClasses);
    }
}
