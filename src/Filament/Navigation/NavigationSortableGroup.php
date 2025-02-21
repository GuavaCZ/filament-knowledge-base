<?php

namespace Guava\FilamentKnowledgeBase\Filament\Navigation;

use Closure;
use Filament\Navigation\NavigationGroup;

class NavigationSortableGroup extends NavigationGroup
{
    protected int|Closure|null $sort = 999999;

    public function sort(int|Closure|null $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function getSort(): int
    {
        return $this->evaluate($this->sort) ?? -1;
    }
}
