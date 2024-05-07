<?php

namespace Guava\FilamentKnowledgeBase\Livewire;

use Arr;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Guava\FilamentKnowledgeBase\Actions\HelpAction;
use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Guava\FilamentKnowledgeBase\Contracts\HasKnowledgeBase;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Livewire\Component;

class HelpMenu extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public array $documentation;

    protected bool $shouldOpenDocumentationInNewTab;

    public function mount(): void
    {
        $controller = request()->route()->controller;

        $this->shouldOpenDocumentationInNewTab = Filament::getPlugin('guava::filament-knowledge-base')->shouldOpenDocumentationInNewTab();

        $this->documentation = Arr::wrap(match (true) {
            $controller instanceof HasKnowledgeBase => $controller::getDocumentation(),
            $controller instanceof Page && in_array(HasKnowledgeBase::class, class_implements($controller::getResource())) => $controller::getResource()::getDocumentation(),
            default => [],
        });
    }

    public function getDocumentation()
    {
        return collect($this->documentation)
            ->map(fn ($documentable) => KnowledgeBase::documentable($documentable))
        ;
    }

    public function actions(): array
    {
        return $this->getDocumentation()
//            ->map(fn (string $class) => KnowledgeBasePanel::getDocumentationAction($class))
            ->map(
                fn (Documentable $documentable) => HelpAction::forDocumentable($documentable)
                    ->openUrlInNewTab($this->shouldOpenDocumentationInNewTab)
            )
            ->toArray()
        ;
    }

    public function shouldShowAsMenu(): bool
    {
        return count($this->documentation) > 1;
    }

    public function getSingleAction(): HelpAction
    {
        return HelpAction::forDocumentable($this->getDocumentation()->first())
            ->generic()
            ->openUrlInNewTab($this->shouldOpenDocumentationInNewTab)
        ;
    }

    public function getMenuAction(): ActionGroup
    {
        return ActionGroup::make($this->actions())
            ->label(__('filament-knowledge-base::translations.help'))
            ->icon('heroicon-o-question-mark-circle')
            ->iconSize('lg')
            ->color('gray')
            ->button()
        ;
    }

    public function render()
    {
        return view('filament-knowledge-base::livewire.help-menu');
    }
}
