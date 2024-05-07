<?php

namespace Guava\FilamentKnowledgeBase;

use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class KnowledgeBasePlugin implements Plugin
{
    protected bool $modalPreviews = false;

    protected bool $slideOverPreviews = false;

    protected bool $modalTitleBreadcrumbs = false;

    protected string $helpMenuRenderHook = PanelsRenderHook::TOPBAR_END;

    public function helpMenuRenderHook(string $renderHook): static
    {
        $this->helpMenuRenderHook = $renderHook;

        return $this;
    }

    public function getHelpMenuRenderHook(): string
    {
        return $this->helpMenuRenderHook;
    }

    public function modalPreviews(bool $condition = true): static
    {
        $this->modalPreviews = $condition;

        return $this;
    }

    public function slideOverPreviews(bool $condition = true): static
    {
        $this->slideOverPreviews = $condition;

        return $this;
    }

    public function modalTitleBreadcrumbs(bool $condition = true): static
    {
        $this->modalTitleBreadcrumbs = $condition;

        return $this;
    }

    public function hasModalPreviews(): bool
    {
        return $this->modalPreviews;
    }

    public function hasSlideOverPreviews(): bool
    {
        return $this->slideOverPreviews;
    }

    public function hasModalTitleBreadcrumbs(): bool
    {
        return $this->modalTitleBreadcrumbs;
    }

    public function getId(): string
    {
        return 'guava::filament-knowledge-base';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->renderHook(
                $this->getHelpMenuRenderHook(),
                fn (): string => Blade::render('@livewire(\'help-menu\')'),
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): string => view('filament-knowledge-base::sidebar-footer', [
                    'active' => Filament::getCurrentPanel()->getId() === config('filament-knowledge-base.panel.id', 'knowledge-base'),
                    'url' => Filament::getPanel(config('filament-knowledge-base.panel.id', 'knowledge-base'))->getUrl(),
                ])
            )
        ;
    }

    public function boot(Panel $panel): void
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
