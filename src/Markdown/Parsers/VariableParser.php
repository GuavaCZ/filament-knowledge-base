<?php

namespace Guava\FilamentKnowledgeBase\Markdown\Parsers;

use Illuminate\Database\Eloquent\Model;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

class VariableParser implements InlineParserInterface
{
    public function __construct(
        protected ?Model $record = null,
    ) {}

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('@var\((.+)\)');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        if (! $this->record) {
            return false;
        }

        $cursor = $inlineContext->getCursor();
        // The @ symbol must not have any other characters immediately prior
        $previousChar = $cursor->peek(-1);
        if ($previousChar !== null && $previousChar !== ' ') {
            // peek() doesn't modify the cursor, so no need to restore state first
            return false;
        }

        // This seems to be a valid match
        // Advance the cursor to the end of the match
        $cursor->advanceBy($inlineContext->getFullMatchLength());

        // Grab the Twitter handle
        [$variable] = $inlineContext->getSubMatches();

        if ($content = $this->record->$variable) {
            $inlineContext->getContainer()->appendChild(new Text($content));

            return true;
        }

        return false;
    }
}
