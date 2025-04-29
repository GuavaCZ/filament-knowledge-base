<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

trait CanDisableFilamentStyles
{
    protected bool $disableFilamentStyledTables = false;

    protected bool $disableFilamentStyledBlockquotes = false;

    public function disableFilamentStyles(bool $condition = true): static
    {
        $this
            ->disableFilamentStyledTables($condition)
            ->disableFilamentStyledBlockquotes($condition)
        ;

        return $this;
    }

    public function disableFilamentStyledTables(bool $condition = true): static
    {
        $this->disableFilamentStyledTables = $condition;

        return $this;
    }

    public function shouldDisableFilamentStyledTables(): bool
    {
        return $this->evaluate($this->disableFilamentStyledTables);
    }

    public function disableFilamentStyledBlockquotes(bool $condition = true): static
    {
        $this->disableFilamentStyledBlockquotes = $condition;

        return $this;
    }

    public function shouldDisableFilamentStyledBlockquotes(): bool
    {
        return $this->evaluate($this->disableFilamentStyledBlockquotes);
    }
}
