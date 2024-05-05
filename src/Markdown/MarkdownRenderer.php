<?php

namespace Guava\FilamentKnowledgeBase\Markdown;

use Guava\FilamentKnowledgeBase\Markdown\Parsers\IncludeParser;
use Guava\FilamentKnowledgeBase\Markdown\Renderers\FencedCodeRenderer;
use Guava\FilamentKnowledgeBase\Markdown\Renderers\ImageRenderer;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Output\RenderedContentInterface;
use N0sz\CommonMark\Marker\Marker;
use N0sz\CommonMark\Marker\MarkerExtension;

final class MarkdownRenderer
{
    protected bool $minimal = false;

    public function minimal(bool $minimal = true): static
    {
        $this->minimal = $minimal;

        return $this;
    }

    public function isMinimal(): bool
    {
        return $this->minimal;
    }

    private function getOptions(): array
    {
        return [
            'default_attributes' => [
                Heading::class => [
                    'class' => static fn (Heading $node) => match ($node->getLevel()) {
                        1 => 'text-3xl mb-2 [&:first-child]:mt-0 mt-10',
                        2 => 'text-xl [&:first-child]:mt-0 mt-2',
                        3 => 'text-lg [&:first-child]:mt-0 mt-2',
                        default => null,
                    } . ' relative',
                ],
                Paragraph::class => [
                    'class' => 'mb-4 leading-relaxed',
                ],
                Marker::class => [
                    'class' => 'bg-primary-500/20 dark:bg-primary-400/40 text-inherit rounded-md py-0.5 px-1.5',
                ],
            ],
            'heading_permalink' => [
                'id_prefix' => '',
                'symbol' => '#',
                'html_class' => 'gu-kb-anchor absolute -left-8 text-primary-600 dark:text-primary-500 font-bold',
            ],
        ];
    }

    private function configureEnvironment(EnvironmentBuilderInterface $environment): EnvironmentInterface
    {
        // Extensions
        $environment
            ->addExtension(new CommonMarkCoreExtension())
            ->addExtension(new DefaultAttributesExtension())
            ->addExtension(new FrontMatterExtension())
            ->addExtension(new MarkerExtension())
        ;
        if (! $this->isMinimal()) {
            $environment
                ->addExtension(new HeadingPermalinkExtension())
            ;
        }

        // Parsers
        $environment->addInlineParser(new IncludeParser($this));

        // Renderers
        $environment
            ->addRenderer(Image::class, new ImageRenderer(), 5)
            ->addRenderer(FencedCode::class, new FencedCodeRenderer(), 5)
        ;

        return $environment;
    }

    private function getEnvironment(): EnvironmentInterface
    {
        return $this->configureEnvironment(
            environment: new Environment(
                config: $this->getOptions()
            )
        );
    }

    private function getMarkdownConverter(): MarkdownConverter
    {
        return new MarkdownConverter(
            environment: $this->getEnvironment()
        );
    }

    public function convert(string $input): RenderedContentInterface
    {
        return cache()->rememberForever(
            $this->getCacheKey($input),
            fn () => $this->getMarkdownConverter()->convert($input)
        );
    }

    protected function getCacheKey(string $input): string
    {
        $options = json_encode([
            'minimal' => $this->isMinimal(),
        ]);

        return md5("kb.$input.$options");
    }
}
