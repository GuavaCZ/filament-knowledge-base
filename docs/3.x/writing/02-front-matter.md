---
title: Front matter
---

# Front matter

Every markdown file starts with a front matter, a YAML block enclosed by three dashes. It holds the metadata of the page, everything after it is the [content](03-markdown.md):

```md
---
title: Getting started
icon: heroicon-o-home
---

# Getting started

Your content here...
```

## Title

The title is displayed in the navigation, in the header of the page and in the breadcrumbs.

If you don't set one, a prettified version of the file name is used, so `getting-started.md` becomes `Getting Started`.

```yaml
title: Getting started
```

## Icon

The icon displayed next to the item in the navigation. You can use any icon from any blade-icons pack you have installed, not just heroicons.

If you don't set one, the default from the config file is used (`heroicon-o-document` for documentation, `heroicon-o-link` for links).

```yaml
icon: heroicon-o-user
```

## Order

Overrides the position of the item within its group or parent. A lower number is displayed first.

Without an order, items are sorted alphabetically by file name, so numeric prefixes (`01-getting-started.md`, `02-faq.md`) work too.

```yaml
order: 3
```

## Active

Set to `false` to hide the page from the navigation and prevent users from viewing it.

> [!NOTE]
> The page can still be shown in your regular panels through modal previews and help actions.

```yaml
active: false
```

## Type

There are three types of items:

- `documentation`: a regular markdown page. This is the default, so you can omit it.
- `group`: groups other items in the navigation. A group file consists only of front matter and configures the directory of the same name, see [structure](01-structure.md).
- `link`: renders a link to an external URL in the navigation.

```yaml
type: group
```

## URL

Only used by the `link` type — the URL to open when the item is clicked:

```yaml
type: link
url: https://example.com
```

## Slug and ID

The ID of a page is the dot-notation of its file path (`users/roles/admin.md` → `users.roles.admin`), and the slug used in the URL is the same path with slashes. You rarely need to touch these, but both can be overridden:

```yaml
id: my-custom-id
slug: my/custom/slug
```

## Custom keys

Any other key you put in the front matter is kept as custom data on the [flatfile model](../advanced/01-flatfile-model.md), so you can use it in your own code:

```php
data_get(KnowledgeBase::model()::find('...')->getData(), 'my-key');
```
