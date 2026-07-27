---
title: Markdown
---

# Markdown

We use CommonMark as the markdown parser and the league/commonmark php implementation. Check their respective websites for a reference on how to use markdown.

- [CommonMark](https://commonmark.org/)
- [League CommonMark](https://commonmark.thephpleague.com/)

We also added some custom parsers/extensions to the markdown parser, described below.

## Markers

In order to mark some words with your primary theme color, you can use the following syntax:

```
In this example, ==this text== will be marked.
```

## Tables

You can use the regular markdown syntax to render tables styled to match filament tables.

```md
| Syntax     |             Description (center)              |     Foo (right) | Bar (left)      |
|------------|:---------------------------------------------:|----------------:|:----------------|
| Header     |                     Title                     |       Something | Else            |
| Paragraphs |  First paragraph. <br><br> Second paragraph.  | First paragraph | First paragraph |
```

## Quotes

Using the regular markdown syntax for quotes, you can render neat banners such as:

```md
> ⚠️ **Warning:** Make sure that the slug is unique!
```

## Syntax highlighting

We offer syntax highlighting through shiki (requires NodeJS on the server):

- [ShikiJS](https://shiki.style/)
- [Spatie ShikiPHP](https://github.com/spatie/shiki-php)

Because of the additional installation steps, syntax highlighting is **disabled by default**.

To enable it, you MUST have both the npm package `shiki` and `spatie/shiki-php` installed.

Which versions of the shiki packages to choose depends on you. I **highly recommend going with the latest versions**, but if you encounter some issues due to incompatibility with other packages, you might need to downgrade.

Check the table below for compatible versions.

| Shiki PHP Version | Shiki JS Version |
|-------------------|------------------|
| ^2.0              | ^1.0             |
| ^1.3              | ^0.14            |

Installing spatie/shiki-php:

```bash
composer require spatie/shiki-php:"^2.0"
```

Installing shiki:

```bash
npm install shiki@^1.0
```

If you use Herd or another node version manager, you will most likely need to create a symlink to your node version. Please follow the instructions [here](https://github.com/spatie/shiki-php?tab=readme-ov-file#using-node-version-manager).

Then you can enable syntax highlighting using:

```php
use Guava\FilamentKnowledgeBase\Filament\Panels\KnowledgeBasePanel;

KnowledgeBasePanel::configureUsing(
    fn (KnowledgeBasePanel $panel) => $panel
        ->syntaxHighlighting()
);
```

## Vite assets

You can use the default image syntax to include vite assets, as long as you provide the full path from your root project directory:

```md
![my image](/resources/img/my-image.png)
```

## Including other files

We support including markdown files within other files. This is especially useful if you want to organize your markdown or display snippets of a whole documentation as a help button without duplicating your markdown files.

The syntax is as follows:

```md
@include(prologue.getting-started)
```

This is extremely helpful when you want to display help buttons for a concrete component or field, but don't want to deal with duplicated information.

You can simply extract parts of your markdown into smaller markdown files and include them in your main file. That way you can only display the partials in your `Help Actions`.
