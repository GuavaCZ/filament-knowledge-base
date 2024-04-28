<?php

namespace Guava\FilamentKnowledgeBase\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Guava\FilamentKnowledgeBase\FilamentKnowledgeBase
 */
class FilamentKnowledgeBase extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Guava\FilamentKnowledgeBase\FilamentKnowledgeBase::class;
    }
}
