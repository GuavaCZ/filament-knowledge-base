<?php

namespace Guava\FilamentKnowledgeBase\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Guava\FilamentKnowledgeBase\KnowledgeBase
 */
class KnowledgeBase extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Guava\FilamentKnowledgeBase\KnowledgeBase::class;
    }
}
