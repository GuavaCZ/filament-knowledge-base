<?php

namespace Guava\FilamentKnowledgeBase\Plugins;

use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\View\PanelsRenderHook;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableModalLinks;
use Guava\FilamentKnowledgeBase\Concerns\HasKnowledgeBasePanelButton;
use Guava\FilamentKnowledgeBase\Concerns\HasModalPreviews;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

class KnowledgeBaseCompanionPlugin implements Plugin
{
    use CanDisableModalLinks;
    use EvaluatesClosures;
    use HasKnowledgeBasePanelButton;
    use HasModalPreviews;

    public const ID = 'guava::filament-knowledge-base-companion';

    protected string $knowledgeBasePanelId;

    protected bool $modalTitleBreadcrumbs = false;

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

    public function hasModalTitleBreadcrumbs(): bool
    {
        return $this->modalTitleBreadcrumbs;
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
                        fn (): string => Blade::render('filament-panels::components.sidebar.group', [
                            'attributes' => new ComponentAttributeBag([
                                'class' => 'px-4 pb-4 [&_.fi-sidebar-item]:rounded-lg [&_.fi-sidebar-item]:ring-1 [&_.fi-sidebar-item]:ring-gray-950/10 dark:[&_.fi-sidebar-item]:ring-white/20',
                            ]),
                            'label' => null,
                            'items' => [
                                NavigationItem::make(__('filament-knowledge-base::translations.knowledge-base'))
                                    ->url(Filament::getPanel($this->getKnowledgeBasePanelId())->getUrl())
                                    ->icon('heroicon-o-home'),
                            ],
                        ])
                        //                        fn (): string => Blade::render('filament-panels::components.sidebar.item', [
                        //                            'url' => '#',
                        //                            'icon' => 'heroicon-o-user',
                        //                            'slot' => new HtmlString('test'),
                        //                        ]),
                        //                                                fn (): string => $this->getKnowledgeBasePanelButton()->render(),
                    )
            )
//            ->renderHook(
//                PanelsRenderHook::BODY_START,
//                fn () => '<style> .fi-sidebar:not(.fi-sidebar-open) .fi-btn-label { display: none; } .fi-sidebar:not(.fi-sidebar-open) .fi-btn-icon { margin-left: 0.25rem; } </style>'
//            )
        ;
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
