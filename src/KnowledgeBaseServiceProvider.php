<?php

namespace Guava\FilamentKnowledgeBase;

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Guava\FilamentKnowledgeBase\Commands\MakeDocumentationCommand;
use Guava\FilamentKnowledgeBase\Filament\Panels\KnowledgeBasePanel;
use Guava\FilamentKnowledgeBase\Livewire\HelpMenu;
use Guava\FilamentKnowledgeBase\Providers\KnowledgeBasePanelProvider;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class KnowledgeBaseServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('filament-knowledge-base')
            ->hasViews()
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigration('create_filament-knowledge-base_table')
            ->hasCommand(MakeDocumentationCommand::class)
        ;
    }

    public function packageRegistered(): void
    {
        $this->app->register(KnowledgeBasePanelProvider::class);
    }

    public function packageBooted(): void
    {
        Livewire::component('help-menu', HelpMenu::class);

        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_FOOTER,
            fn (): string => view('filament-knowledge-base::sidebar-footer', [
                'active' => Filament::getCurrentPanel()->getId() === config('filament-knowledge-base.panel.id', 'knowledge-base'),
                'url' => Filament::getPanel(config('filament-knowledge-base.panel.id', 'knowledge-base'))->getUrl(),
            ])
        );
    }
}
