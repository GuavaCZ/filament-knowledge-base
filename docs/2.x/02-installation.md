---
title: Installation
---

# Installation

You can install the package via composer:

```bash
composer require guava/filament-knowledge-base:"^2.0"
```

Next, install `@tailwindcss/typography` if you don't have it already, since we use `prose` to style the markdown output:

```bash
npm install -D @tailwindcss/typography
```

Then publish the package assets:

```bash
php artisan filament:assets
```

## Setup

If you don't have a separate panel yet for your knowledge base, please create one using the built in filament command:

```bash
php artisan make:filament-panel
```

For example, you might create a panel named `knowledge-base`.

Next, add the `KnowledgeBasePlugin` plugin to your **knowledge base panel** service provider:

```php
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;

$panel->plugin(KnowledgeBasePlugin::make());
```

Similarly, add the `KnowledgeBaseCompanionPlugin` plugin to your **regular panel** service provider:

```php
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBaseCompanionPlugin;

$panel->plugin(KnowledgeBaseCompanionPlugin::make()
    ->knowledgeBasePanelId('knowledge-base') // Put your knowledge base panel ID here
);
```

> [!NOTE]
> A custom filament theme is **required** for the plugin to work!
>
> If you don't have one, please refer to the [filament documentation](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) on how to create a custom theme.

And lastly, add the following to your custom filament theme to correctly build the CSS and use the required tailwind plugins:

```css
@plugin "@tailwindcss/typography";
@source '../../../../vendor/guava/filament-knowledge-base/src/**/*';
@source '../../../../vendor/guava/filament-knowledge-base/resources/views/**/*';
```

> [!IMPORTANT]
> It is **important** that both your filament knowledge base panel and your regular panel(s) use a custom theme with these source paths. It's up to you if you want to use the same theme for all panels or different themes for each.

## Creating documentation pages

By default, all your documentation files should live in the `docs/<panel-id>` directory in the root of your project. The structure is covered in detail on the [markdown files](05-markdown-files.md) page.

To create documentation pages, simply run the `docs:make` command and follow the instructions:

```bash
php artisan docs:make
```

This will create a basic empty documentation file. To edit it, simply open it in your favorite editor.

An example of a documentation page:

```md
---
title: Introduction
---
# Introduction
This is my first documentation page
```

If you visit your regular filament panel, you should now see a button at the bottom of your sidebar that will lead you to your knowledge base panel with your first documentation page!
