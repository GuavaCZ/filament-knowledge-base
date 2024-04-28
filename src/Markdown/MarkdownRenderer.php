<?php

namespace Guava\FilamentKnowledgeBase\Markdown;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\Node\Block\Paragraph;
use SimonVomEyser\CommonMarkExtension\LazyImageExtension;
use Spatie\LaravelMarkdown\MarkdownRenderer as BaseMarkdownRenderer;

class MarkdownRenderer extends BaseMarkdownRenderer
{
    public function __construct(array $commonmarkOptions = [], bool $highlightCode = true, string $highlightTheme = 'github-light', bool | string | null $cacheStoreName = null, bool $renderAnchors = true, bool $renderAnchorsAsLinks = false, array $extensions = [], array $blockRenderers = [], array $inlineRenderers = [], array $inlineParsers = [])
    {
        parent::__construct($commonmarkOptions, $highlightCode, $highlightTheme, $cacheStoreName, $renderAnchors, $renderAnchorsAsLinks, $extensions, $blockRenderers, $inlineRenderers, $inlineParsers);

        $this->renderAnchors(false);

        $this->commonmarkOptions([
            'default_attributes' => [
                Heading::class => [
                    'class' => static fn (Heading $node) => match ($node->getLevel()) {
                        1 => 'text-3xl mb-2 [&:first-child]:mt-0 mt-8',
                        2 => 'text-xl',
                        default => null,
                    } . ' relative',
                ],
                Paragraph::class => [
                    'class' => 'mb-4',
                ],
                Code::class => [
                    'class' => 'bg-primary-600/10 dark:bg-primary-600/30'
                ]
            ],
            'heading_permalink' => [
                'id_prefix' => '',
                'symbol' => '#',
                'html_class' => 'absolute -left-8 text-primary-600 dark:text-primary-500 font-bold',
            ]
        ]);
    }

    public function configureCommonMarkEnvironment(EnvironmentBuilderInterface $environment): void
    {
        parent::configureCommonMarkEnvironment($environment);

        $environment->addExtension(new FrontMatterExtension());
        $environment->addExtension(new DefaultAttributesExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
//        $environment->addExtension(new TableOfContentsExtension()); // TODO: Make optional
    }
}
