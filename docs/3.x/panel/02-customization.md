---
title: Customization
---

# Customization

All options on this page are configured on the `KnowledgeBasePlugin` in your knowledge base panel provider.

## Table of contents

A table of contents is generated for each documentation page and rendered as a sidebar on the right side of the page.

You can move it to the left:

```php
use Guava\FilamentKnowledgeBase\Enums\TableOfContentsPosition;

$plugin->tableOfContentsPosition(TableOfContentsPosition::Start);
```

Or disable it entirely:

```php
$plugin->disableTableOfContents();
```

## Anchors

Anchors are the little symbols rendered in front of each heading, linking to that section of the page.

By default we use the `#` symbol. You can change it:

```php
$plugin->anchorSymbol('¶');
```

Or disable anchors entirely:

```php
$plugin->disableAnchors();
```

## Breadcrumbs

Each documentation page has a breadcrumb navigation at the top, which is especially useful for nested pages. You can disable it:

```php
$plugin->disableBreadcrumbs();
```

## Article class

The container where the markdown is rendered has a `gu-kb-article` class, which you can use to target it with your own CSS. You can also add your own class(es):

```php
$plugin->articleClass('max-w-2xl');
```

## Filament styles

Tables and blockquotes in your markdown are styled to match filament by default. To render them unstyled:

```php
$plugin->disableFilamentStyles();

// Or individually
$plugin->disableFilamentStyledTables();
$plugin->disableFilamentStyledBlockquotes();
```

## Guest access and authorization

Since the knowledge base is a regular panel, access control works like in any other panel. To make it publicly accessible, set up [guest access](https://filamentphp.com/docs/5.x/users/overview#setting-up-guest-access-to-a-panel) like you would for any panel, and to restrict it, use the `FilamentUser` contract on your `User` model as described in the [filament documentation](https://filamentphp.com/docs/5.x/users/overview#authorizing-access-to-the-panel).
