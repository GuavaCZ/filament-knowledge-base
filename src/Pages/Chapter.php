<?php

namespace Guava\FilamentKnowledgeBase\Pages;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Concerns\EvaluatesClosures;
use Guava\FilamentKnowledgeBase\Pages\Concerns\HasIcon;
use Guava\FilamentKnowledgeBase\Pages\Concerns\HasLabel;

class Chapter extends Page
{
    use EvaluatesClosures;
    use HasIcon;
    use HasLabel;

    protected static string $view = 'filament-knowledge-base::pages.section';

    public function getNavigationItem(): NavigationItem
    {
        return NavigationItem::make($this->getLabel())
            ->icon($this->getIcon())
            ->url(static::getUrl())
        ;
    }

    public static function __callStatic($name, $arguments)
    {
        if ($name === 'getNavigationItem') {
            return (new static())->getNavigationItem();
        }
    }
}
