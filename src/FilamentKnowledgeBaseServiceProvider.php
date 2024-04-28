<?php

namespace Guava\FilamentKnowledgeBase;

use Filament\Facades\Filament;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Guava\FilamentKnowledgeBase\Commands\FilamentKnowledgeBaseCommand;
use Guava\FilamentKnowledgeBase\Livewire\HelpMenu;
use Guava\FilamentKnowledgeBase\Markdown\MarkdownRenderer;
use Guava\FilamentKnowledgeBase\Providers\KnowledgeBasePanelProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentKnowledgeBaseServiceProvider extends PackageServiceProvider
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
            ->hasMigration('create_filament-knowledge-base_table')
            ->hasCommand(FilamentKnowledgeBaseCommand::class)
        ;
    }

    public function packageRegistered(): void
    {
        $this->app->register(KnowledgeBasePanelProvider::class);
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Js::make('shiki', __DIR__ . '/../node_modules/shiki/dist/index.mjs'),
        ]);

        Filament::serving(function () {
            $url = Arr::first(Filament::getPanel('knowledge-base')->getPages())::getUrl(panel: 'knowledge-base');

            FilamentView::registerRenderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn (): string => '<div class="fi-kb-placeholder [&:not(:has(div))]:hidden"></div>'
            );
            FilamentView::registerRenderHook(
                PanelsRenderHook::TOPBAR_END,
                fn (): string => Blade::render('@livewire(\'help-menu\')'),
            );
            FilamentView::registerRenderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): string => Blade::render(<<<blade
<x-filament::button
class="m-4 mx-8"
    color="gray"
    icon="heroicon-o-book-open"
    href="$url"
    tag="a"
>
Dokumentace
</x-filament::button>
blade)
            );
        });

        Livewire::component('help-menu', HelpMenu::class);
        //        $this->app->singleton(MarkdownRenderer::class, function () {
        //            return new MarkdownRenderer();
        //        });
        //
        //        RenderedContentWithFrontMatter::setRenderer($this->app->make(MarkdownRenderer::class));
    }
}
