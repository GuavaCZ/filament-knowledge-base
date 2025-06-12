<?php

namespace Guava\FilamentKnowledgeBase\Enums;

use Filament\Pages\Enums\SubNavigationPosition;

enum TableOfContentsPosition
{
    case Start;

    case End;

    public function toSubNavigationPosition(): SubNavigationPosition
    {
        return match ($this) {
            self::Start => SubNavigationPosition::Start,
            self::End => SubNavigationPosition::End,
        };
    }
}
