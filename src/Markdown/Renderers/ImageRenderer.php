<?php

namespace Guava\FilamentKnowledgeBase\Markdown\Renderers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\NodeIterator;
use League\CommonMark\Node\StringContainerInterface;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\RegexHelper;
use League\CommonMark\Xml\XmlNodeRendererInterface;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

final class ImageRenderer implements ConfigurationAwareInterface, NodeRendererInterface, XmlNodeRendererInterface
{
    /** @psalm-readonly-allow-private-mutation */
    private ConfigurationInterface $config;

    /**
     * @param  Image  $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable
    {
        Image::assertInstanceOf($node);

        $attrs = $node->data->get('attributes');

        $forbidUnsafeLinks = ! $this->config->get('allow_unsafe_links');
        if ($forbidUnsafeLinks && RegexHelper::isLinkPotentiallyUnsafe($node->getUrl())) {
            $attrs['src'] = '';
        } else {
            $attrs['src'] = Str::startsWith($url = $node->getUrl(), ['http://', 'https://'])
                ? $url
                : Vite::asset(str($url)->ltrim('/')->toString());
        }

        $attrs['alt'] = $this->getAltText($node);

        if (($title = $node->getTitle()) !== null) {
            $attrs['title'] = $title;
        }

        if (str($node->getUrl())->endsWith('.mov')) {
            return new HtmlElement('video', ['muted' => 'muted', 'autoplay' => 'autoplay', 'loop' => 'loop',
                'class' => 'rounded-md ring-1 ring-gray-950/5 dark:ring-white/10',
            ], new HtmlElement('source', $attrs), true);
        } else {
            return new HtmlElement('img', $attrs, '', true);
        }
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    public function getXmlTagName(Node $node): string
    {
        return 'image';
    }

    /**
     * @param  Image  $node
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        Image::assertInstanceOf($node);

        return [
            'destination' => $node->getUrl(),
            'title' => $node->getTitle() ?? '',
        ];
    }

    private function getAltText(Image $node): string
    {
        $altText = '';

        foreach ((new NodeIterator($node)) as $n) {
            if ($n instanceof StringContainerInterface) {
                $altText .= $n->getLiteral();
            } elseif ($n instanceof Newline) {
                $altText .= "\n";
            }
        }

        return $altText;
    }
}
