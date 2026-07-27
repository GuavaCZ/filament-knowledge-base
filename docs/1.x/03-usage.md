---
title: Usage
---

# Usage

## Register plugin

To begin, register the `KnowledgeBasePlugin` in all your filament panels from which you want to access your knowledge base:

```php
use Filament\Panel;
use Guava\FilamentKnowledgeBase\KnowledgeBasePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            // ...
            KnowledgeBasePlugin::make(),
        ]);
}
```

The knowledge base itself lives in its own panel, which the package registers for you (its ID and path are configurable in the config file).

## Create documentation

To create your first documentation, simply run the `docs:make` command, such as:

```bash
php artisan docs:make prologue.getting-started
```

This will create a file in `/docs/en/prologue/getting-started.md`.

If you want to create the file for a specific locale, you can do so using the `--locale` option (can be repeated for multiple locales):

```bash
php artisan docs:make prologue.getting-started --locale=de --locale=en
```

This would create the file for both the `de` and `en` locale.

If you **don't** pass any locale, it will automatically create the documentation file for every locale in `/docs/{locale}`.

## Markdown files

A markdown file consists of two sections, the front matter and the content.

In the front matter, you can customize the documentation page:

```md
---
title: My example documentation
icon: heroicon-o-book-open
---
```

The following options are available:

- `title`: the title of the documentation page.
- `icon`: the icon of the documentation page. You can use any name supported by Blade UI icons that you have installed.
- `order`: the order of the page within its parent / group. A lower number will be displayed first.
- `group`: the group (and its title) of the documentation page.
- `parent`: the parent of the documentation page. So for a file in `docs/en/prologue/getting-started/intro.md`, the parent would be `getting-started`.

Anything after the front matter is your content, written in markdown. See the [markdown](05-markdown.md) page for everything our renderer supports.

## Accessing the knowledge base

In every panel you registered the plugin, we automatically inject a documentation button at the very bottom of the sidebar. But we offer a deeper integration to your panels.

### Integrating into resources or pages

To integrate your resource with the documentation, all you need to do is implement the `HasKnowledgeBase` contract in your resource or page.

This will require you to implement the `getDocumentation` method, where you simply return the documentation pages you want to integrate. You can either return the `IDs` as strings (dot-separated path inside `/docs/{locale}/`) or use the helper to retrieve the model:

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
            KnowledgeBase::model()::find('users.permissions'),
        ];
    }
}
```

This will render a help menu button at the end of the top navbar.

If you add more than one documentation file, it will render a dropdown menu, otherwise the help button will directly reference the documentation you linked.

## Modal links

To make it easy to access the documentation from anywhere, this plugin intercepts fragment links anywhere in the filament panel in order to open up a modal for a documentation page.

To use modal links, simply add a link in **any place** in your panel with a fragment in the format `#modal-<documentation-id>`, for example:

```html
<a href="#modal-intro.getting-started">Open Introduction</a>
```

As long as a documentation with that ID exists (`/docs/en/intro/getting-started.md`), it will automatically open a modal with the content of that documentation.

You can even share the URL with someone and it will automatically open the modal upon opening!

To disable modal links, simply call `disableModalLinks()` on the `KnowledgeBasePlugin` in your panel service provider.

## Help actions

The plugin comes with a neat `HelpAction`, which can be linked to a specific markdown file or even a partial markdown file.

For example, the "What is a slug?" help was added using the following:

```php
use Guava\FilamentKnowledgeBase\Actions\Forms\Components\HelpAction;

->hintAction(HelpAction::forDocumentable('projects.creating-projects.slug')
    ->label('What is a slug?')
    ->slideOver(false)
),
```

## Accessing the documentation models

We use the `sushi` package in the background to store the documentations. This way, they behave almost like regular eloquent models.

To get the model, simply use our helper `KnowledgeBase::model()`:

```php
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;

// find specific model
KnowledgeBase::model()::find('<id>');
// query models
KnowledgeBase::model()::query()->where('title', 'Some title');
// etc.
```

## Cache

By default, the package caches all markdown files to ensure a smooth and fast user experience. If you don't see your changes, make sure to clear the cache:

```bash
php artisan cache:clear
```
