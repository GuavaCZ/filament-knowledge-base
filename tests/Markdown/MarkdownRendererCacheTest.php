<?php

use Filament\Panel;
use Guava\FilamentKnowledgeBase\KnowledgeBase;
use Guava\FilamentKnowledgeBase\Markdown\MarkdownRenderer;
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;
use Illuminate\Support\Facades\Cache;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Output\RenderedContentInterface;

beforeEach(function () {
    // The renderer reads its options off the current panel's plugin, and these
    // tests deliberately don't boot a panel, so hand it a bare plugin instead.
    app()->instance(KnowledgeBase::class, new class extends KnowledgeBase
    {
        public function plugin(Panel | string | null $panel = null): KnowledgeBasePlugin
        {
            return KnowledgeBasePlugin::make();
        }
    });

    config()->set('cache.default', 'array');
    Cache::flush();
});

function cacheKeyFor(MarkdownRenderer $renderer, string $input): string
{
    return (function () use ($input) {
        return $this->getCacheKey($input);
    })->call($renderer);
}

it('caches plain data rather than the rendered content object', function () {
    $renderer = new MarkdownRenderer;
    $input = "---\ntitle: Hello\n---\n\n# Heading\n";

    $renderer->convert($input);

    $cached = Cache::get(cacheKeyFor($renderer, $input));

    expect($cached)->toBeArray()
        ->and($cached)->toHaveKeys(['content', 'front-matter'])
        ->and($cached['content'])->toBeString()
        ->and($cached['front-matter'])->toBe(['title' => 'Hello'])
    ;
});

it('returns the same html on a cache hit', function () {
    $renderer = new MarkdownRenderer;
    $input = "# Heading\n\nSome *emphasised* text.\n";

    $first = (string) $renderer->convert($input);
    $second = (string) $renderer->convert($input);

    expect($second)->toBe($first)
        ->and($second)->toContain('Heading')
    ;
});

it('still exposes the parsed document on a cache hit', function () {
    $renderer = new MarkdownRenderer;
    $input = "# Heading\n";

    $renderer->convert($input);

    // The AST is never cached, so this is re-parsed on demand. The @include
    // parser and FlatfileNode::getAnchors() both rely on it.
    $document = $renderer->convert($input)->getDocument();

    expect($document)->toBeInstanceOf(Document::class)
        ->and($document->hasChildren())->toBeTrue()
    ;
});

it('preserves front matter on a cache hit', function () {
    $renderer = new MarkdownRenderer;
    $input = "---\ntitle: Cached\nicon: heroicon-o-book-open\n---\n\n# Heading\n";

    $renderer->convertAndReturnFluent($input);
    $result = $renderer->convertAndReturnFluent($input);

    expect($result->get('front-matter'))->toBe([
        'title' => 'Cached',
        'icon' => 'heroicon-o-book-open',
    ]);
});

it('defaults front matter to an empty array when the document has none', function () {
    $renderer = new MarkdownRenderer;
    $input = "# Heading\n";

    expect($renderer->convertAndReturnFluent($input)->get('front-matter'))->toBe([])
        ->and($renderer->convertAndReturnFluent($input)->get('front-matter'))->toBe([])
    ;
});

it('discards a value cached by an older version of the package', function () {
    $renderer = new MarkdownRenderer;
    $input = "# Heading\n";

    // What a pre-fix cache entry decays into once its class can't be resolved.
    Cache::forever(
        cacheKeyFor($renderer, $input),
        unserialize('O:40:"League\CommonMark\Output\RenderedContent":0:{}', ['allowed_classes' => false]),
    );

    $result = $renderer->convert($input);

    expect($result)->toBeInstanceOf(RenderedContentInterface::class)
        ->and((string) $result)->toContain('Heading')
        ->and(Cache::get(cacheKeyFor($renderer, $input)))->toBeArray()
    ;
});

it('rejects an invalid cache ttl', function (mixed $ttl) {
    config()->set('filament-knowledge-base.cache.ttl', $ttl);

    (new MarkdownRenderer)->convert('# Heading');
})->throws(InvalidArgumentException::class)->with([
    'zero' => 0,
    'negative' => -1,
    'zero as a string' => '0',
    'not a number' => 'soon',
    'null' => null,
    'true' => true,
]);

it('accepts a numeric string cache ttl', function (mixed $ttl) {
    // env() doesn't cast numeric strings, so a .env FILAMENT_KB_CACHE_TTL
    // arrives as a string.
    config()->set('filament-knowledge-base.cache.ttl', $ttl);

    $renderer = new MarkdownRenderer;
    $input = "# Heading\n";

    $renderer->convert($input);

    expect(Cache::get(cacheKeyFor($renderer, $input)))->toBeArray();
})->with([
    'int' => 3600,
    'string' => '3600',
]);

it('supports caching forever', function () {
    config()->set('filament-knowledge-base.cache.ttl', 'forever');

    $renderer = new MarkdownRenderer;
    $input = "# Heading\n";

    $renderer->convert($input);

    expect(Cache::get(cacheKeyFor($renderer, $input)))->toBeArray();
});
