<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;

trait HasKnowledgeBasePanelButton
{
    protected ?Closure $modifyKnowledgeBasePanelButtonUsing = null;

    protected bool $disableKnowledgeBasePanelButton = false;

    protected bool $openKnowledgeBasePanelInNewTab = false;

    public function modifyKnowledgeBasePanelButtonUsing(?Closure $using = null): static
    {
        $this->modifyKnowledgeBasePanelButtonUsing = $using;

        return $this;
    }

    public function openKnowledgeBasePanelInNewTab(bool $condition = true): static
    {
        $this->openKnowledgeBasePanelInNewTab = $condition;

        return $this;
    }

    public function shouldOpenKnowledgeBasePanelInNewTab(): bool
    {
        return $this->openKnowledgeBasePanelInNewTab;
    }

    public function getKnowledgeBasePanelButton(): Action
    {
        $default = $this->getDefaultKnowledgeBasePanelButton();

        return $this->evaluate($this->modifyKnowledgeBasePanelButtonUsing, [
            'action' => $default,
        ]) ?? $default;
    }

    protected function getDefaultKnowledgeBasePanelButton(): Action
    {
        return Action::make(__('filament-knowledge-base::translations.knowledge-base'))
            ->icon('heroicon-o-book-open')
            ->color('gray')
            ->extraAttributes([
                'class' => '!font-medium !text-gray-700 dark:!text-gray-200 !mx-4 !mt-2 !mb-4 !py-2.5 !bg-transparent !flex !gap-0 space-x-1.5 !justify-start hover:!bg-gray-100 focus-visible:!bg-gray-100 dark:hover:!bg-white/5 dark:focus-visible:!bg-white/5',
            ])
            ->url(KnowledgeBase::url(
                Filament::getPanel($this->getKnowledgeBasePanelId())
            ), $this->shouldOpenKnowledgeBasePanelInNewTab())
        ;
    }

    public function disableKnowledgeBasePanelButton(bool $condition = true): static
    {
        $this->disableKnowledgeBasePanelButton = $condition;

        return $this;
    }

    public function shouldDisableKnowledgeBasePanelButton(): bool
    {
        return $this->disableKnowledgeBasePanelButton;
    }
}
