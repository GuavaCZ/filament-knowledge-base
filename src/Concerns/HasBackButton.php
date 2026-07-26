<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

use Closure;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;

trait HasBackButton
{
    protected string | Closure | null $backUrl = null;

    protected ?Closure $modifyBackButtonUsing = null;

    protected bool $disableBackButton = false;

    public function backUrl(string | Closure | null $url): static
    {
        $this->backUrl = $url;

        return $this;
    }

    public function getBackUrl(): string
    {
        return $this->evaluate($this->backUrl) ?? KnowledgeBase::url(Filament::getDefaultPanel());
    }

    public function modifyBackButtonUsing(?Closure $using = null): static
    {
        $this->modifyBackButtonUsing = $using;

        return $this;
    }

    public function getBackButton(): NavigationItem
    {
        $default = $this->getDefaultBackButton();

        return $this->evaluate($this->modifyBackButtonUsing, [
            'item' => $default,
        ]) ?? $default;
    }

    protected function getDefaultBackButton(): NavigationItem
    {
        return NavigationItem::make(__('filament-knowledge-base::translations.back'))
            ->url($this->getBackUrl())
            ->icon('heroicon-o-arrow-uturn-left')
        ;
    }

    public function disableBackButton(bool $condition = true): static
    {
        $this->disableBackButton = $condition;

        return $this;
    }

    public function shouldDisableBackButton(): bool
    {
        return $this->disableBackButton;
    }
}
