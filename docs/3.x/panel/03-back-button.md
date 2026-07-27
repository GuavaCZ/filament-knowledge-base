---
title: Back button
---

# Back button

At the bottom of the knowledge base sidebar, a "Back" button sends the user back to your app. It mirrors the "Knowledge base" button that the [companion plugin](../companion/01-setup.md) renders in your regular panels.

By default, it links to your default panel.

## Customizing the URL

If your users should be sent back to a different panel (or any other URL), you can change it:

```php
use Filament\Facades\Filament;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;

$plugin->backUrl(fn () => KnowledgeBase::url(Filament::getPanel('app')));
```

A plain string works too:

```php
$plugin->backUrl('/admin');
```

## Customizing the button

To modify the button itself (label, icon, ...):

```php
use Filament\Navigation\NavigationItem;

$plugin->modifyBackButtonUsing(fn (NavigationItem $item) => $item
    ->label('Back to app')
    ->icon('heroicon-o-arrow-left'));
```

## Disabling the button

```php
$plugin->disableBackButton();
```
