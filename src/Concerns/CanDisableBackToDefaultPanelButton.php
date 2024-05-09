<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

trait CanDisableBackToDefaultPanelButton
{
    protected bool $disableBackToDefaultPanelButton = false;

    public function disableBackToDefaultPanelButton(bool $condition = true): static
    {
        $this->disableBackToDefaultPanelButton = $condition;

        return $this;
    }

    public function shouldDisableBackToDefaultPanelButton(): bool
    {
        return $this->disableBackToDefaultPanelButton;
    }
}
