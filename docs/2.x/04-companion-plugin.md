---
title: Companion plugin
---

# Companion plugin

## Introduction

The `KnowledgeBaseCompanionPlugin` is the second plugin this package comes with. It is registered in your **regular filament panels** and integrates them with your knowledge base panel(s).

It renders a "Knowledge base" button at the bottom of the sidebar, a help menu in the topbar for resources and pages linked with documentation, and lets you open documentation pages in modals right inside your panel.

## Usage

Add the plugin to your regular panel(s) and tell it which panel your knowledge base is:

```php
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBaseCompanionPlugin;

$panel->plugin(KnowledgeBaseCompanionPlugin::make()
    ->knowledgeBasePanelId('knowledge-base')
);
```

## Help menu

To integrate a resource or page with the documentation, implement the `HasKnowledgeBase` contract and return the documentation pages from the `getDocumentation` method. You can either return the IDs as strings (dot-separated path inside your docs directory) or use the helper to retrieve the model:

```php
use Guava\FilamentKnowledgeBase\Contracts\HasKnowledgeBase;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;

class UserResource extends Resource implements HasKnowledgeBase
{
    // ...

    public static function getDocumentation(): array
    {
        return [
            'users.introduction',
            'users.authentication',
            KnowledgeBase::model()::find('knowledge-base.users.permissions'),
        ];
    }
}
```

This will render a help button at the end of the top navbar. If you add more than one documentation file, it will render a dropdown menu, otherwise the help button will directly reference the documentation you linked.

### Customizing the render hook

If you want to place the help menu someplace else, you can override the render hook:

```php
use Filament\View\PanelsRenderHook;

$plugin->helpMenuRenderHook(PanelsRenderHook::TOPBAR_START);
```

## Modal previews

Instead of redirecting the user to the documentation immediately, you can render the documentation in a modal right inside your regular panel:

```php
$plugin->modalPreviews();
```

If you prefer slide overs, you can additionally also enable them:

```php
$plugin->slideOverPreviews();
```

### Breadcrumbs in modal titles

When using modal previews, by default the title shows just that, the title of the documentation page. If you'd rather show the full breadcrumb to the documentation page, you may enable it like so:

```php
$plugin->modalTitleBreadcrumbs();
```

## Modal links

The plugin intercepts fragment links anywhere in the filament panel in order to open up a modal for a documentation page.

To use modal links, simply add a link in **any place** in your panel with a fragment in the format `#modal-<documentation-id>`, for example:

```html
<a href="#modal-intro.getting-started">Open Introduction</a>
```

As long as a documentation with that ID exists, it will automatically open a modal with the content of that documentation.

You can even share the URL with someone and it will automatically open the modal upon opening!

To disable modal links:

```php
$plugin->disableModalLinks();
```

## Help actions

The plugin comes with a neat `HelpAction`, which can be linked to a specific markdown file or even a partial markdown file:

```php
use Guava\FilamentKnowledgeBase\Actions\HelpAction;

TextInput::make('slug')
    ->hintAction(
        HelpAction::forDocumentable('projects.creating-projects.slug')
            ->label('What is a slug?')
    );
```

This is extremely helpful when you want to display help buttons for a concrete component or field. Extract parts of your markdown into smaller partial files, include them in your main file via `@include(...)` and link only the partials to your help actions.

## Knowledge base button

The plugin renders a "Knowledge base" button at the bottom of the sidebar, linking to your knowledge base panel. You can disable it if you like:

```php
$plugin->disableKnowledgeBasePanelButton();
```

### Opening the knowledge base in a new tab

By default, links to the knowledge base open in the same tab. To change this:

```php
$plugin->openKnowledgeBasePanelInNewTab();
```
