<?php

namespace Guava\FilamentKnowledgeBase\Markdown;

use Arr;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Guava\FilamentKnowledgeBase\Filament\Panels\KnowledgeBasePanel;
use Guava\FilamentKnowledgeBase\Markdown\Parsers\IncludeParser;
use Guava\FilamentKnowledgeBase\Markdown\Renderers\FencedCodeRenderer;
use Guava\FilamentKnowledgeBase\Markdown\Renderers\ImageRenderer;
use InvalidArgumentException;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
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
        $anchorSymbol = KnowledgeBase::panel()->getAnchorSymbol();
        $shouldDisableDefaultClasses = KnowledgeBase::panel()->shouldDisableDefaultClasses();

        return [
            'default_attributes' => [
                Heading::class => [
                    'class' => $shouldDisableDefaultClasses
                        ? 'relative'
                        : static fn (Heading $node) => match ($node->getLevel()) {
                            1 => 'text-3xl mb-2 [&:first-child]:mt-0 mt-10',
                            2 => 'text-xl mb-2 [&:first-child]:mt-0 mt-2',
                            3 => 'text-lg mb-1 [&:first-child]:mt-0 mt-2',
                            default => null,
                        } . ' relative',
                ],
                Paragraph::class => [
                    'class' => $shouldDisableDefaultClasses ? '' : 'mb-4 [&:last-child]:mb-0 leading-relaxed',
                ],
                Marker::class => [
                    'class' => 'bg-primary-500/20 dark:bg-primary-400/40 text-inherit rounded-md py-0.5 px-1.5',
                ],
                BlockQuote::class => [
                    'class' => $shouldDisableDefaultClasses ? '' : 'bg-white dark:bg-gray-900 mt-2 mb-4 p-4 rounded-md ring-1 ring-gray-950/5 dark:ring-white/10',
                ],
            ],
            'heading_permalink' => [
                'id_prefix' => '',
                'symbol' => $anchorSymbol ?? '',
                'html_class' => Arr::toCssClasses([
                    'gu-kb-anchor md:absolute md:-left-8 mr-2 md:mr-0 text-primary-600 dark:text-primary-500 font-bold',
                    'hidden' => ! $anchorSymbol,
                ]),
            ],
            'table' => [
                'wrap' => [
                    'enabled' => true,
                    'tag' => 'div',
                    'attributes' => [
                        'class' => $shouldDisableDefaultClasses ? '' : Arr::toCssClasses([
                            'divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10',
                            'fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10 !border-t-0',
                            '[&_table]:fi-ta-table [&_table]:w-full [&_table]:table-auto [&_table]:divide-y [&_table]:divide-gray-200 [&_table]:text-start [&_table]:dark:divide-white/5',
                            '[&_thead]:divide-y [&_thead]:divide-gray-200 [&_thead]:dark:divide-white/5',
                            '[&_thead_tr]:bg-gray-50 [&_thead_tr]:dark:bg-white/5',
                            '[&_thead_th]:text-start',
                            '[&_th]:px-3 [&_th]:py-3.5',
                            '[&_td]:px-3 [&_td]:py-3.5',
                            '[&_tbody]:divide-y [&_tbody]:divide-gray-200 [&_tbody]:whitespace-nowrap [&_tbody]:dark:divide-white/5',
                        ]),
                    ],
                ],
                'alignment_attributes' => [
                    'left' => ['class' => '!text-start'],
                    'center' => ['class' => '!text-center'],
                    'right' => ['class' => '!text-end'],
                ],
            ],
        ];
    }

    private function configureEnvironment(EnvironmentBuilderInterface $environment): EnvironmentInterface
    {
        // Extensions
        $environment
            ->addExtension(new CommonMarkCoreExtension)
            ->addExtension(new DefaultAttributesExtension)
            ->addExtension(new FrontMatterExtension)
            ->addExtension(new MarkerExtension)
            ->addExtension(new TableExtension)
        ;
        if (! $this->isMinimal()) {
            $environment
                ->addExtension(new HeadingPermalinkExtension)
            ;
        }

        // Parsers
        $environment->addInlineParser(new IncludeParser($this));

        // Renderers
        $environment
            ->addRenderer(Image::class, new ImageRenderer, 5)
        ;

        if (KnowledgeBasePanel::hasSyntaxHighlighting()) {
            $environment
                ->addRenderer(FencedCode::class, new FencedCodeRenderer, 5)
            ;
        }

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
        $ttl = config('filament-knowledge-base.cache.ttl');

        if ($ttl === 'forever') {
            return cache()->rememberForever($this->getCacheKey($input), fn () => $this->getMarkdownConverter()->convert($input));
        }

        if (! is_int($ttl) || $ttl < 1) {
            throw new InvalidArgumentException('The cache.ttl configuration must be an integer greater than 0 or the string "forever".');
        }

        return cache()->remember($this->getCacheKey($input), $ttl, fn () => $this->getMarkdownConverter()->convert($input));
    }

    protected function getCacheKey(string $input): string
    {
        $options = json_encode([
            'minimal' => $this->isMinimal(),
        ]);

        return config('filament-knowledge-base.cache.prefix') . md5("kb.$input.$options");
    }
}
