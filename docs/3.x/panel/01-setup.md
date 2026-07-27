---
title: Setup
---

# Setup

A knowledge base needs its **own, separate panel**, since the plugin takes over the panel navigation. If you don't have one yet, create it with the built in filament command:

```bash
php artisan make:filament-panel knowledge-base
```

This creates a new panel provider in `app/Providers/Filament/KnowledgeBasePanelProvider.php`.

Then register the plugin on it:

```php
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->id('knowledge-base')
        ->path('kb')
        // ...
        ->plugins([
            KnowledgeBasePlugin::make(),
        ]);
}
```

And that's it! The knowledge base is now available under `/kb`, or whatever you configured in the `path` option of the panel.

> [!NOTE]
> The panel needs a custom theme with our source paths, otherwise nothing will be styled. See [installation](../02-installation.md).

## Custom docs path

By default, your markdown files live in `docs/<panel-id>` in the root of your project. You can point the plugin somewhere else:

```php
KnowledgeBasePlugin::make(base_path('kb/my-knowledge-base'));
```

How the files inside that directory are organized is covered in [writing documentation](../writing/01-structure.md).

## It's a regular panel

The knowledge base panel is a regular filament panel, so branding, colors, middleware and everything else is configured in its panel provider like in any other panel:

```php
$panel
    ->id('knowledge-base')
    ->path('kb')
    ->brandName('My Docs')
    ->plugin(KnowledgeBasePlugin::make());
```
