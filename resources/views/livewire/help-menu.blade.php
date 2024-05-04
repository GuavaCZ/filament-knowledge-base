@php
    use Filament\Facades\Filament;
    $hasModalPreviews = Filament::getPlugin('guava::filament-knowledge-base')->hasModalPreviews();
    $hasSlideOverPreviews = Filament::getPlugin('guava::filament-knowledge-base')->hasSlideOverPreviews();
    $hasModalTitleBreadcrumbs = Filament::getPlugin('guava::filament-knowledge-base')->hasModalTitleBreadcrumbs();
@endphp

<div @class([
    'hidden' => ! $documentation,
])
>
    @if($documentation)
        @if($this->shouldShowAsMenu())
            {{$this->getMenuAction()}}
        @else
            {{ $this->getSingleAction() }}
        @endif
    @endif

    <x-filament-actions::modals/>

    @push('scripts')
        @if($hasModalPreviews)
            @foreach($this->getDocumentation() as $documentable)
                <x-filament::modal id="{{$documentable->getId()}}"
                                   :close-by-clicking-away="true"
                                   :close-button="true"
                                   width="2xl"
                                   :slide-over="$hasSlideOverPreviews"
                                   class="[&_.fi-modal-content]:px-12 [&_.fi-modal-content]:gap-y-0"
                                   :footer-actions-alignment="\Filament\Support\Enums\Alignment::End"
                                   :sticky-footer="true"
                                   :sticky-header="true">

                    <x-slot name="heading">
                        @if($hasModalTitleBreadcrumbs && !empty($documentable->getBreadcrumbs()))
                            {{ KnowledgeBase::breadcrumbs($documentable) }}
                        @else
                            {{ $documentable->getTitle() }}
                        @endif
                    </x-slot>
                    {!! $documentable->getSimpleHtml() !!}
                    <x-slot name="footerActions">
                        <x-filament::button tag="a"
                                            :href="$documentable->getUrl()">
                            {{ __('filament-knowledge-base::translations.open-documentation') }}
                        </x-filament::button>
                        <x-filament::button color="gray"
                                            x-on:click.prevent="$dispatch('close-modal', { id: '{{$documentable->getId()}}' })">

                            {{ __('filament-knowledge-base::translations.close') }}
                        </x-filament::button>
                    </x-slot>
                </x-filament::modal>
            @endforeach
        @endif
    @endpush
</div>
