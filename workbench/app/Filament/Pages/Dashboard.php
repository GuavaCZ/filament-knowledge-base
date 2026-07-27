<?php

namespace Workbench\App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Guava\FilamentKnowledgeBase\Contracts\HasKnowledgeBase;

class Dashboard extends BaseDashboard implements HasKnowledgeBase
{
    public static function getDocumentation(): array | string
    {
        return [
            'getting-started',
            'features.code-highlighting',
        ];
    }
}
