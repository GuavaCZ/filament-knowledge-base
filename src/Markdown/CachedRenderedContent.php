<?php

namespace Guava\FilamentKnowledgeBase\Markdown;

use Closure;
use League\CommonMark\Extension\FrontMatter\FrontMatterProviderInterface;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Output\RenderedContentInterface;

/**
 * A rendered markdown document restored from the cache.
 *
 * Only the HTML and front matter are cached, since a serialized AST can come
 * back as an __PHP_Incomplete_Class. The AST is re-parsed on demand.
 */
class CachedRenderedContent implements FrontMatterProviderInterface, RenderedContentInterface
{
    protected ?Document $document = null;

    /**
     * @param  Closure(): Document  $documentResolver
     */
    public function __construct(
        protected string $content,
        protected mixed $frontMatter,
        protected Closure $documentResolver,
    ) {}

    public function getDocument(): Document
    {
        return $this->document ??= ($this->documentResolver)();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return mixed|null
     */
    public function getFrontMatter()
    {
        return $this->frontMatter;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
