<?php

// config for Guava/KnowledgeBasePanel
return [
    'panel' => [
        'id' => env('FILAMENT_KB_ID', 'knowledge-base'),
        'path' => env('FILAMENT_KB_PATH', 'kb'),
    ],

    'docs-path' => env('FILAMENT_KB_DOCS_PATH', 'docs'),

    'model' => \Guava\FilamentKnowledgeBase\Models\FlatfileNode::class,

    'cache' => [
        'prefix' => env('FILAMENT_KB_CACHE_PREFIX', 'filament_kb_'),
        'ttl' => env('FILAMENT_KB_CACHE_TTL', 86400),
    ],
];
