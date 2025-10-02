# Knowledge Base Plugin

## Introduction

The `KnowledgeBasePlugin` is one of the two plugins this package comes with and is the primary plugin you will use.

This plugin turns any panel you add it to into a knowledge base.

It is currently **required** to use a custom, separate panel for your knowledge base, as it will override the sidebar navigation.

## Usage

If you don't have a separate panel yet for your knowledge base, please create one using the built in filament command:
```bash
php artisan filament:panel
```

For example, you might create a panel named `knowledge-base`.

Next, add the `KnowledgeBasePlugin` plugin to your panel service provider:

```php
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;

$panel->plugin(KnowledgeBasePlugin::make())
```
## Customization

There are a lot of customization options available for your knowledge base. All options are configured via the `KnowledgeBasePlugin` added to your knowledge base panel.

We currently support the following options:

### Custom documentation path

By default, all documentation files live in the `docs/<panel-id>` directory in the root of your project.

If you want to change this path, you can do so when registering the plugin:

```php
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;

KnowledgeBasePlugin::make(base_path('kb/my-knowledge-base'));
```

### Table of contents

By default, a table of contents is generated for each documentation file and rendered as a sidebar on the right side of the page.

#### Changing the position of the table of contents

You can change the position of the table of contents using the `tableOfContentsPosition` option.

The available options are:
- `TableOfContentsPosition::Start`
- `TableOfContentsPosition::End` (default)

```php
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;

KnowledgeBasePlugin::make()
        ->tableOfContentsPosition(TableOfContentsPosition::Start);
```

#### Disabling the table of contents
If you don't want to use the table of contents, you can disable it using the `disableTableOfContents` option:

```php
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;

KnowledgeBasePlugin::make()
    ->disableTableOfContents();
```

### Anchors

Anchors are automatically generated symbols in front of each heading in your documentation files. They act as links to specific sections of the documentation.

#### Customizing the anchor symbol

By default, we use the `#` symbol. You can customize the symbol using:

```php
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;

KnowledgeBasePlugin::make()
        ->anchorSymbol('¶');
```

#### Disabling anchors

If you don't want to use anchors, you can disable them using the `disableAnchors` option:

```php
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;

KnowledgeBasePlugin::make()
        ->disableAnchors();
```

### Breadcrubs

Each documentation page has a breadcrumb navigation at the top of the page. This is especially useful for nested documentation pages to navigate back to previous pages.

#### Disable breadcrumbs

If you don't want the breadcrumb navigation, you can disable it if you wish:

```php
use \Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;

KnowledgeBasePlugin::make()
        ->disableBreadcrumbs();
```

### Guest Access

Previously in version 1.x, guest access had to be enabled specifically via a plugin option.

In version 2.x and up, you have full control over your knowledge base panel and thus can enable guest access just like you would for any other panel.

Please [visit the filament documentation](https://filamentphp.com/docs/3.x/panels/users#setting-up-guest-access-to-a-panel) to learn how to enable guest access.

### Authorization

Similarly, authorization is handled just like any other panel. In your `User` model, you can implement the `FilamentUser` interface to control access to different knowledge base panels.

More information in the [filament documentation](https://filamentphp.com/docs/3.x/panels/users#authorizing-access-to-the-panel).
