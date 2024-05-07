<?php

namespace Guava\FilamentKnowledgeBase;

use Guava\FilamentKnowledgeBase\Commands\MakeDocumentationCommand;
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
    }
}
