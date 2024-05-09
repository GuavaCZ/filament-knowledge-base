<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

trait CanDisableKnowledgeBasePanelButton
{
    protected bool $disableKnowledgeBasePanelButton = false;

    public function disableKnowledgeBasePanelButton(bool $condition = true): static
    {
        $this->disableKnowledgeBasePanelButton = $condition;

        return $this;
    }

    public function shouldDisableKnowledgeBasePanelButton(): bool
    {
        return $this->disableKnowledgeBasePanelButton;
    }
}
