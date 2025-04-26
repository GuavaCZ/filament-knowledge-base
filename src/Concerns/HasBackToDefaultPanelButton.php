<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;

trait HasBackToDefaultPanelButton
{
    protected ?Closure $modifyBackToDefaultPanelButtonUsing = null;

    protected bool $disableBackToDefaultPanelButton = false;

    public function modifyBackToDefaultPanelButtonUsing(?Closure $using = null): static
    {
        $this->modifyBackToDefaultPanelButtonUsing = $using;

        return $this;
    }

    public function getBackToDefaultPanelButton(): Action
    {
        $default = $this->getDefaultBackToDefaultPanelButton();

        return $this->evaluate($this->modifyBackToDefaultPanelButtonUsing, [
            'action' => $default,
        ]) ?? $default;
    }

    protected function getDefaultBackToDefaultPanelButton(): Action
    {
        return Action::make(__('filament-knowledge-base::translations.back-to-default-panel'))
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('gray')
            ->extraAttributes([
                'class' => '!font-medium !text-gray-700 dark:!text-gray-200 !mx-4 !mt-2 !mb-4 !py-2.5 !bg-transparent !flex !justify-start hover:!bg-gray-100 focus-visible:!bg-gray-100 dark:hover:!bg-white/5 dark:focus-visible:!bg-white/5',
            ])
            ->url(KnowledgeBase::url(Filament::getDefaultPanel()))
        ;
    }

    public function disableBackToDefaultPanelButton(bool $condition = true): static
    {
        $this->disableBackToDefaultPanelButton = $condition;

        return $this;
    }

    public function shouldDisableBackToDefaultPanelButton(): bool
    {
        return $this->disableBackToDefaultPanelButton;
    }
}
