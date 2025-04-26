<?php

namespace Guava\FilamentKnowledgeBase\Plugins;

use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableKnowledgeBasePanelButton;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableModalLinks;
use Guava\FilamentKnowledgeBase\Concerns\HasModalPreviews;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Illuminate\Support\Facades\Blade;

class KnowledgeBaseCompanionPlugin implements Plugin
{
    use CanDisableKnowledgeBasePanelButton;
    use CanDisableModalLinks;
    use HasModalPreviews;

    public const ID = 'guava::filament-knowledge-base-companion';

    protected string $knowledgeBasePanelId;

    protected bool $modalTitleBreadcrumbs = false;

    protected bool $openDocumentationInNewTab = false;

    protected string $helpMenuRenderHook = PanelsRenderHook::TOPBAR_END;

    public function knowledgeBasePanelId(string $id): static
    {
        $this->knowledgeBasePanelId = $id;

        return $this;
    }

    public function getKnowledgeBasePanelId(): string
    {
        return $this->knowledgeBasePanelId;
    }

    public function helpMenuRenderHook(string $renderHook): static
    {
        $this->helpMenuRenderHook = $renderHook;

        return $this;
    }

    public function getHelpMenuRenderHook(): string
    {
        return $this->helpMenuRenderHook;
    }

    public function modalTitleBreadcrumbs(bool $condition = true): static
    {
        $this->modalTitleBreadcrumbs = $condition;

        return $this;
    }

    public function openDocumentationInNewTab(bool $condition = true): static
    {
        $this->openDocumentationInNewTab = $condition;

        return $this;
    }

    public function hasModalTitleBreadcrumbs(): bool
    {
        return $this->modalTitleBreadcrumbs;
    }

    public function shouldOpenDocumentationInNewTab(): bool
    {
        return $this->openDocumentationInNewTab;
    }

    public function getId(): string
    {
        return static::ID;
    }

    public function register(Panel $panel): void
    {
        $panel
            ->renderHook(
                $this->getHelpMenuRenderHook(),
                fn (): string => Blade::render('@livewire(\'help-menu\')'),
            )
            ->when(
                ! $this->shouldDisableModalLinks(),
                fn (Panel $panel) => $panel->renderHook(
                    PanelsRenderHook::BODY_END,
                    fn (): string => Blade::render('@livewire(\'modals\')'),
                )
            )
            ->when(
                ! $this->shouldDisableKnowledgeBasePanelButton(),
                fn (Panel $panel) => $panel
                    ->renderHook(
                        PanelsRenderHook::SIDEBAR_FOOTER,
                        fn (): string => view('filament-knowledge-base::sidebar-action', [
                            'label' => __('filament-knowledge-base::translations.knowledge-base'),
                            'icon' => 'heroicon-o-book-open',
                            'url' => KnowledgeBase::url(
                                Filament::getPanel($this->getKnowledgeBasePanelId())
                            ),
                            'shouldOpenUrlInNewTab' => $this->shouldOpenDocumentationInNewTab(),
                        ])
                    )
            )
        ;
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
