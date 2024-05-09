<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

trait HasModalPreviews
{
    protected bool $modalPreviews = false;

    protected bool $slideOverPreviews = false;

    public function modalPreviews(bool $condition = true): static
    {
        $this->modalPreviews = $condition;

        return $this;
    }

    public function slideOverPreviews(bool $condition = true): static
    {
        $this->slideOverPreviews = $condition;

        return $this;
    }

    public function hasModalPreviews(): bool
    {
        return $this->modalPreviews;
    }

    public function hasSlideOverPreviews(): bool
    {
        return $this->slideOverPreviews;
    }
}
