<?php

namespace Guava\FilamentKnowledgeBase\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Guava\FilamentKnowledgeBase\Contracts\HasKnowledgeBase;
use Guava\FilamentKnowledgeBase\Facades\FilamentKnowledgeBase;
use Livewire\Component;

class HelpMenu extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public array|string $documentation;

    public function mount(): void
    {
        $controller = request()->route()->controller;

        $this->documentation = match (true) {
            $controller instanceof HasKnowledgeBase => $controller::getDocumentation(),
            $controller instanceof Page && in_array(HasKnowledgeBase::class, class_implements($controller::getResource())) => $controller::getResource()::getDocumentation(),
            default => [],
        };
    }

    public function actions(): array
    {
        return collect($this->documentation)
//            ->map(fn (string $class) => FilamentKnowledgeBase::getDocumentationAction($class))
            ->map(fn (string $class) => FilamentKnowledgeBase::getDocumentationAction($class))
            ->toArray()
        ;
    }

    public function render()
    {
        return view('filament-knowledge-base::livewire.help-menu');
    }
}
