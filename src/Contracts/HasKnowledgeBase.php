<?php

namespace Guava\FilamentKnowledgeBase\Contracts;

interface HasKnowledgeBase
{
    public static function getDocumentation(): array | string;
}
