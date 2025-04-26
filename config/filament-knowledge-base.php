<?php

// config for Guava/KnowledgeBasePanel
return [
    'docs-path' => env('FILAMENT_KB_DOCS_PATH', 'docs'),

    'model' => \Guava\FilamentKnowledgeBase\Models\FlatfileDocumentation::class,

    'cache' => [
        'prefix' => env('FILAMENT_KB_CACHE_PREFIX', 'filament_kb_'),
        'ttl' => env('FILAMENT_KB_CACHE_TTL', 'forever'),
    ],
];
