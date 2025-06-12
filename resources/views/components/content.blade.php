@use(Guava\FilamentKnowledgeBase\Facades\KnowledgeBase)

@push('styles')
    <style>
        html.dark .phiki,
        html.dark .phiki span {
            color: var(--phiki-dark-color) !important;
            background-color: var(--phiki-dark-background-color) !important;
            font-style: var(--phiki-dark-font-style) !important;
            font-weight: var(--phiki-dark-font-weight) !important;
            text-decoration: var(--phiki-dark-text-decoration) !important;
        }
        pre.phiki code span.line span.line-number {
            /*content: attr(data-line);*/
            display: inline-block;
            width: 1.7rem;
            margin-right: 1rem;
            color: #666;
            text-align: right;
        }
    </style>
@endpush

<article
    {{ $attributes->class([
        'gu-kb-article prose dark:prose-invert',
    ]) }}
    x-ignore
    x-load
    x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('anchors-component', 'guava/filament-knowledge-base') }}"
    x-data="anchorsComponent()"
>
    {{ $slot }}
</article>
