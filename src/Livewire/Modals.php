<?php

namespace Guava\FilamentKnowledgeBase\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Livewire\Attributes\On;
use Livewire\Component;

class Modals extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?Documentable $documentable = null;

    protected bool $shouldOpenDocumentationInNewTab;

    public function mount(): void
    {
        $this->shouldOpenDocumentationInNewTab = Filament::getPlugin('guava::filament-knowledge-base')->shouldOpenDocumentationInNewTab();
    }

    #[On('close-modal')]
    public function onClose($id)
    {
        if ($id !== 'kb-custom-modal') {
            return;
        }
        $this->js(<<<'JS'
$nextTick(() => {
    $wire.resetDocumentation();
});
JS);
    }

    public function showDocumentation(string $id)
    {
        $this->documentable = KnowledgeBase::documentable($id);

        $this->js(<<<'JS'
$nextTick(() => {
    $dispatch('open-modal', { id: 'kb-custom-modal' });
});
JS);
    }

    public function resetDocumentation()
    {
        $this->documentable = null;
    }

    public function render()
    {
        return view('filament-knowledge-base::livewire.modals');
    }
}
