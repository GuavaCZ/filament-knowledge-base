<?php

namespace Guava\FilamentKnowledgeBase\Pages\Concerns;

use Closure;
use Illuminate\Support\Str;

trait HasIcon
{
    protected Closure | string | null $icon = null;

    public function icon(string | Closure $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }
}
