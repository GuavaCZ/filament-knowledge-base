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
                'class' => '!mx-4 !mb-4 !py-2.5 !flex space-x-1.5 !justify-start',
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
