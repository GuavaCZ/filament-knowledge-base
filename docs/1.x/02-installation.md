---
title: Installation
---

# Installation

You can install the package via composer:

```bash
composer require guava/filament-knowledge-base:"^1.0"
```

Make sure to publish the package assets using:

```bash
php artisan filament:assets
```

and translations with:

```bash
php artisan vendor:publish --tag="filament-knowledge-base-translations"
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-knowledge-base-config"
```

This is the contents of the published config file:

```php
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
```

## Custom theme

A custom filament theme is required. Check the [filament documentation](https://filamentphp.com/docs/3.x/panels/themes#creating-a-custom-theme) on how to create one.

You can create one specifically for the knowledge base panel, or if you want to have the same design as your main panel(s), you can simply reuse the vite theme from your panel.

Then in the register method of your `AppServiceProvider`, configure the vite theme of the knowledge base panel using:

```php
use Guava\FilamentKnowledgeBase\Filament\Panels\KnowledgeBasePanel;

KnowledgeBasePanel::configureUsing(
    fn (KnowledgeBasePanel $panel) => $panel
        ->viteTheme('resources/css/filament/admin/theme.css') // your filament vite theme path here
);
```

## Build CSS

In every filament theme, make sure to include the plugin's php and blade files in the `tailwind.config.js`, so the CSS is correctly built:

```js
{
    content: [
        //...

        './vendor/guava/filament-knowledge-base/src/**/*.php',
        './vendor/guava/filament-knowledge-base/resources/**/*.blade.php',
    ]
}
```
