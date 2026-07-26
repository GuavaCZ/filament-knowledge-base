<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

trait CanDisableOpenDocumentationButton
{
    protected bool $disableOpenDocumentationButton = false;

    public function disableOpenDocumentationButton(bool $condition = true): static
    {
        $this->disableOpenDocumentationButton = $condition;

        return $this;
    }

    public function shouldDisableOpenDocumentationButton(): bool
    {
        return $this->disableOpenDocumentationButton;
    }
}
