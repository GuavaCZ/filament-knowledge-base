<?php

namespace Guava\FilamentKnowledgeBase;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class KnowledgeBasePlugin implements Plugin
{
    protected bool $modalPreviews = false;

    protected bool $slideOverPreviews = false;

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

    public function hasModalPreviews(): bool
    {
        return $this->modalPreviews;
    }

    public function hasSlideOverPreviews(): bool
    {
        return $this->slideOverPreviews;
    }

    public function getId(): string
    {
        return 'guava::filament-knowledge-base';
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook(
            PanelsRenderHook::TOPBAR_END,
            fn (): string => Blade::render('@livewire(\'help-menu\')'),
        );
    }

    public function boot(Panel $panel): void
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
