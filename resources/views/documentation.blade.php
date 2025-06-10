@php
    use Filament\Pages\Enums\SubNavigationPosition;
    use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;

    $sidebar = $this->getSubNavigationPosition();
    $articleClass = KnowledgeBase::plugin()->getArticleClass();
@endphp

@push('scripts')
    <script>
        document.querySelectorAll('.gu-kb-anchor')
            .forEach((element) => element.addEventListener('click', function (event) {
                window.Livewire.dispatch('documentation.anchor.copy', {
                    url: event.target.href
                });
            }))

        var filamentKnowledgeBaseTranslations = {
            urlCopied: "@lang('filament-knowledge-base::translations.url-copied')",
            codeCopied: "@lang('filament-knowledge-base::translations.code-copied')",
        };
    </script>
@endpush
{{--<x-filament-panels::sidebar.item activ--}}
<x-filament-panels::page
    @class([
        "[&_.fi-page-sub-navigation-sidebar-ctn]:sticky [&_.fi-page-sub-navigation-sidebar-ctn]:top-20",
        "[&_.fi-page-sub-navigation-sidebar]:pl-4 [&_.fi-page-sub-navigation-sidebar]:ml-4 [&_.fi-page-sub-navigation-sidebar]:border-l [&_.fi-page-sub-navigation-sidebar]:border-l-gray-600/10 [&_.fi-page-sub-navigation-sidebar]:dark:border-l-gray-600/30" => $sidebar === SubNavigationPosition::End,
        "[&_.fi-page-sub-navigation-sidebar]:pr-4 [&_.fi-page-sub-navigation-sidebar]:mr-4 [&_.fi-page-sub-navigation-sidebar]:border-r [&_.fi-page-sub-navigation-sidebar]:border-r-gray-600/10 [&_.fi-page-sub-navigation-sidebar]:dark:border-r-gray-600/30" => $sidebar === SubNavigationPosition::Start,
    ])
    :full-height="true"
>
    <x-filament-knowledge-base::content @class([
        "gu-kb-article-full",
        $articleClass => ! empty($articleClass),
    ])>
        {!!  $this->record->getHtml() !!}
    </x-filament-knowledge-base::content>


</x-filament-panels::page>
