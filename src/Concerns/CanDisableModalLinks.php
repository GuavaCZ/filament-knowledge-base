<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

trait CanDisableModalLinks
{
    protected bool $disableModalLinks = false;

    public function disableModalLinks(bool $condition = true): static
    {
        $this->disableModalLinks = $condition;

        return $this;
    }

    public function shouldDisableModalLinks(): bool
    {
        return $this->disableModalLinks;
    }
}
