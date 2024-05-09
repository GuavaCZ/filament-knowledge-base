<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

trait CanDisableBreadcrumbs
{
    protected bool $disableBreadcrumbs = false;

    public function disableBreadcrumbs(bool $condition = true): static
    {
        $this->disableBreadcrumbs = $condition;

        return $this;
    }

    public function shouldDisableBreadcrumbs(): bool
    {
        return $this->evaluate($this->disableBreadcrumbs);
    }
}
