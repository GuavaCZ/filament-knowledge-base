@php
    use Filament\Facades\Filament;
    use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;

    $plugin = KnowledgeBase::plugin();
    $companion = KnowledgeBase::companion();

    $hasModalPreviews = $companion->hasModalPreviews();
    $hasSlideOverPreviews = $companion->hasSlideOverPreviews();
    $hasModalTitleBreadcrumbs = $companion->hasModalTitleBreadcrumbs();
    $target = $companion->shouldOpenKnowledgeBasePanelInNewTab() ? '_blank' : '_self';
    $articleClass = $plugin->getArticleClass();
@endphp

<div
    x-ignore
    x-load
    x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('modals-component', 'guava/filament-knowledge-base') }}"
    x-data="modalsComponent()"
>
    @if($documentable)
        <x-filament::modal id="kb-custom-modal"
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

            <x-filament-knowledge-base::content @class([
            "gu-kb-article-modal",
            $articleClass => ! empty($articleClass),
        ])>
                {!! $documentable->getSimpleHtml() !!}
            </x-filament-knowledge-base::content>
            <x-slot name="footerActions">
                <x-filament::button tag="a"
                                    :href="$documentable->getUrl()"
                                    :target="$target">
                    {{ __('filament-knowledge-base::translations.open-documentation') }}
                </x-filament::button>
                <x-filament::button color="gray"
                                    x-on:click.prevent="$dispatch('close-modal', { id: 'kb-custom-modal' })">

                    {{ __('filament-knowledge-base::translations.close') }}
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    @endif
</div>
