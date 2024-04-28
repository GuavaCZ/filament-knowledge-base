<?php

namespace Guava\FilamentKnowledgeBase\Pages\Concerns;

use Closure;
use Illuminate\Support\Str;

trait HasLabel
{
    protected Closure | string | null $label = null;

    public function label(string | Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->evaluate($this->label) ?? Str::headline(class_basename($this));
    }
}
