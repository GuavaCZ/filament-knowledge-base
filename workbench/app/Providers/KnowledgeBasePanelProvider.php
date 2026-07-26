<?php

namespace Workbench\App\Providers;

use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Workbench\App\Http\Middleware\AutoLogin;

class KnowledgeBasePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('knowledge-base')
            ->path('kb')
            ->colors(['primary' => Color::Amber])
            ->theme('workbench')
            ->plugins([
                // Docs live inside the workbench, not in the skeleton's base_path,
                // so the path is passed explicitly.
                KnowledgeBasePlugin::make(dirname(__DIR__, 2) . '/docs/knowledge-base'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                ConvertEmptyStringsToNull::class,
                DispatchServingFilamentEvent::class,
                DisableBladeIconComponents::class,
                SubstituteBindings::class,
                AutoLogin::class,
            ], isPersistent: true)
        ;
    }
}
