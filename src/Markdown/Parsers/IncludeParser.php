<?php

namespace Guava\FilamentKnowledgeBase\Markdown\Parsers;

use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
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

        // Grab the id from the match
        [$path] = $inlineContext->getSubMatches();

        $result = $this->renderer->convert(
            file_get_contents(
                str(KnowledgeBase::plugin()->getDocsPath())
                    ->rtrim(DIRECTORY_SEPARATOR)
                    ->append(
                        DIRECTORY_SEPARATOR,
                        \App::getLocale(),
                        DIRECTORY_SEPARATOR,
                        str($path)
                            ->trim(DIRECTORY_SEPARATOR)
                            ->replaceEnd('.md', '')
                            ->replace('.', DIRECTORY_SEPARATOR)
                            ->append('.md')
                    ),
            )
        );

        $inlineContext->getContainer()->replaceWith($result->getDocument());

        return true;
    }
}
