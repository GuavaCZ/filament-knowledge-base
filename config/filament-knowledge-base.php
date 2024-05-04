<?php

// config for Guava/FilamentKnowledgeBase
return [
    'panel' => [
        'id' => env('FILAMENT_KB_ID', 'knowledge-base'),
        'path' => env('FILAMENT_KB_PATH', 'kb'),
        'guest-access' => env('FILAMENT_KB_GUEST_ACCESS', true),
        'theme-path' => 'resources/css/filament/admin/theme.css',
    ],

    'docs-path' => env('FILAMENT_KB_DOCS_PATH', 'docs'),

    'model' => \Guava\FilamentKnowledgeBase\Models\FlatfileDocumentation::class,

    'file-name' => '\d+-(.*)(?=\.)',
];
