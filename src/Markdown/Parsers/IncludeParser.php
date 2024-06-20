<?php

namespace Guava\FilamentKnowledgeBase\Markdown\Parsers;

use Guava\FilamentKnowledgeBase\Markdown\MarkdownRenderer;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

class IncludeParser implements InlineParserInterface
{
    public function __construct(protected MarkdownRenderer $renderer) {}

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('@include\((.+)\)');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        // The @ symbol must not have any other characters immediately prior
        $previousChar = $cursor->peek(-1);
        if ($previousChar !== null && $previousChar !== ' ' && $previousChar !== "\n") {
            // peek() doesn't modify the cursor, so no need to restore state first
            return false;
        }

        // This seems to be a valid match
        // Advance the cursor to the end of the match
        $cursor->advanceBy($inlineContext->getFullMatchLength());

        // Grab the Twitter handle
        [$path] = $inlineContext->getSubMatches();

        $result = $this->renderer->convert(
            file_get_contents(
                str(base_path(config('filament-knowledge-base.docs-path')))
                    ->rtrim('/')
                    ->append(
                        '/',
                        \App::getLocale(),
                        '/',
                        str($path)
                            ->trim('/')
                            ->replaceEnd('.md', '')
                            ->replace('.', '/')
                            ->append('.md')
                    ),
            )
        );

        $inlineContext->getContainer()->replaceWith($result->getDocument());

        return true;
    }
}
