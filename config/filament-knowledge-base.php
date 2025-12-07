<?php

// config for Guava/KnowledgeBasePanel
use Guava\FilamentKnowledgeBase\Enums\NodeType;

return [
    'flatfile-model' => \Guava\FilamentKnowledgeBase\Models\FlatfileNode::class,

    'cache' => [
        'prefix' => env('FILAMENT_KB_CACHE_PREFIX', 'filament_kb_'),
        'ttl' => env('FILAMENT_KB_CACHE_TTL', 86400),
    ],

    'icons' => [
        NodeType::Documentation->value => 'heroicon-o-document',
        NodeType::Link->value => 'heroicon-o-link',
        NodeType::Group->value => null,
    ],
];
