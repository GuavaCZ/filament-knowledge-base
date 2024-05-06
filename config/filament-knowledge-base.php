<?php

// config for Guava/KnowledgeBasePanel
return [
    'panel' => [
        'id' => env('FILAMENT_KB_ID', 'knowledge-base'),
        'path' => env('FILAMENT_KB_PATH', 'kb'),
    ],

    'docs-path' => env('FILAMENT_KB_DOCS_PATH', 'docs'),

    'model' => \Guava\FilamentKnowledgeBase\Models\FlatfileDocumentation::class,
];
