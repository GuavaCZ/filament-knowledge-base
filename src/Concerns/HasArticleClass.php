<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

use Closure;

trait HasArticleClass
{
    protected null | Closure | string $articleClass = null;

    public function articleClass(Closure | string $class): static
    {
        $this->articleClass = $class;

        return $this;
    }

    public function getArticleClass(): ?string
    {
        return $this->evaluate($this->articleClass);
    }
}
