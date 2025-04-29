<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

use Guava\FilamentKnowledgeBase\Enums\TableOfContentsPosition;

trait HasTableOfContents
{
    protected bool $disableTableOfContents = false;

    protected TableOfContentsPosition $tableOfContentsPosition = TableOfContentsPosition::End;

    public function disableTableOfContents(bool $condition = true): static
    {
        $this->disableTableOfContents = $condition;

        return $this;
    }

    public function shouldDisableTableOfContents(): bool
    {
        return $this->evaluate($this->disableTableOfContents);
    }

    public function tableOfContentsPosition(TableOfContentsPosition $position): static
    {
        $this->tableOfContentsPosition = $position;

        return $this;
    }

    public function getTableOfContentsPosition(): TableOfContentsPosition
    {
        return $this->evaluate($this->tableOfContentsPosition);
    }
}
