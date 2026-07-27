---
title: Customization
---

# Customization

The knowledge base panel is configured through `KnowledgeBasePanel::configureUsing` in the register method of a service provider, options on the `KnowledgeBasePlugin` are configured where you registered it.

## Customize the knowledge base panel

```php
use Guava\FilamentKnowledgeBase\Filament\Panels\KnowledgeBasePanel;

KnowledgeBasePanel::configureUsing(
    fn (KnowledgeBasePanel $panel) => $panel
        // Your options here
);
```

### Change brand name

For example to change the default brand name/title (displayed in the top left) of the panel, you can do:

```php
KnowledgeBasePanel::configureUsing(
    fn (KnowledgeBasePanel $panel) => $panel
        ->brandName('My Docs')
);
```

### Custom classes on documentation article

By default, the documentation article (the container where the markdown content is rendered) has a `gu-kb-article` class, which you can use to target and modify. You can also add your own class(es) using:

```php
KnowledgeBasePanel::configureUsing(
    fn (KnowledgeBasePanel $panel) => $panel
        ->articleClass('max-w-2xl')
);
```

To disable the default styling altogether, you can use:

```php
KnowledgeBasePanel::configureUsing(
    fn (KnowledgeBasePanel $panel) => $panel
        ->disableDefaultClasses()
);
```

### Table of contents

By default, in each documentation article there is a table of contents sidebar on the right. You can disable it or change its position:

```php
use Guava\FilamentKnowledgeBase\Enums\TableOfContentsPosition;

KnowledgeBasePanel::configureUsing(
    fn (KnowledgeBasePanel $panel) => $panel
        ->disableTableOfContents()
        // or
        ->tableOfContentsPosition(TableOfContentsPosition::Start)
);
```

### Anchors

We render an anchor prefix (`#`) in front of every heading. You can customize the symbol or disable it:

```php
KnowledgeBasePanel::configureUsing(
    fn (KnowledgeBasePanel $panel) => $panel
        ->anchorSymbol('¶')
        // or
        ->disableAnchors()
);
```

### Breadcrumbs

By default on each documentation page, there is a breadcrumb at the top. You can disable it if you wish:

```php
KnowledgeBasePanel::configureUsing(
    fn (KnowledgeBasePanel $panel) => $panel
        ->disableBreadcrumbs()
);
```

### Disable the back to default panel button

When in the knowledge base panel, a button is rendered to go back to the default filament panel. You can disable it:

```php
KnowledgeBasePanel::configureUsing(
    fn (KnowledgeBasePanel $panel) => $panel
        ->disableBackToDefaultPanelButton()
);
```

### Guest access

By default, the panel is only accessible to authenticated users.

If you want the knowledge base to be publicly accessible, simply configure it like so:

```php
KnowledgeBasePanel::configureUsing(
    fn (KnowledgeBasePanel $panel) => $panel
        ->guestAccess()
);
```

## Plugin options

### Disable the knowledge base panel button

When in a panel where the plugin is enabled, we render by default in the bottom of the sidebar a button to go to the knowledge base panel. You can disable it if you like:

```php
$plugin->disableKnowledgeBasePanelButton();
```

### Customize the help menu/button render hook

If you want to place the help menu / button someplace else, you can override the render hook:

```php
use Filament\View\PanelsRenderHook;

$plugin->helpMenuRenderHook(PanelsRenderHook::TOPBAR_START);
```

### Enable modal previews

If you want to open documentations in modal previews instead of immediately redirecting to the full pages, you can enable it like this:

```php
$plugin->modalPreviews();
```

If you prefer to use slide overs, you can additionally also enable them:

```php
$plugin->slideOverPreviews();
```

### Enable breadcrumbs in modal preview titles

When using modal previews, by default the title shows just that, the title of the documentation page.

If you'd rather show the full breadcrumb to the documentation page, you may enable it like so:

```php
$plugin->modalTitleBreadcrumbs();
```

### Open documentation links in new tab

When you open a documentation, by default it will be opened in the same tab.

To change this, you can customize your plugin:

```php
$plugin->openDocumentationInNewTab();
```
