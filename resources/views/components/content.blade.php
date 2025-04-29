@use(Guava\FilamentKnowledgeBase\Facades\KnowledgeBase)
<article
    {{ $attributes->class([
        'gu-kb-article prose dark:prose-invert',
    ]) }}
    x-ignore
    ax-load
    ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('anchors-component', 'guava/filament-knowledge-base') }}"
    x-data="anchorsComponent()"
>
    {{ $slot }}
</article>
