---
title: Installation
---

# Installation

## Requirements

- PHP 8.2 or higher
- Laravel 12 or higher
- Filament 5.x

## Installing the package

You can install the package via composer:

```bash
composer require guava/filament-knowledge-base
```

Next, install `@tailwindcss/typography` if you don't have it already, since we use `prose` to style the markdown output:

```bash
npm install -D @tailwindcss/typography
```

Then publish the package assets:

```bash
php artisan filament:assets
```

## Custom theme

A custom filament theme is **required**, otherwise the CSS of the knowledge base is not built and the pages will look broken.

If you don't have one yet, please read the [filament documentation](https://filamentphp.com/docs/5.x/styling/overview#creating-a-custom-theme) on how to create a custom theme.

Once you have a theme, add the following to your **theme.css** file:

```css
@plugin "@tailwindcss/typography";
@source '../../../../vendor/guava/filament-knowledge-base/src/**/*';
@source '../../../../vendor/guava/filament-knowledge-base/resources/views/**/*';
```

> [!IMPORTANT]
> Both your knowledge base panel and every regular panel that uses the companion plugin need a theme with these source paths. It's up to you if you want to use the same theme for all panels or a separate one for each.

## Translations and config

Optionally, you can publish the translations:

```bash
php artisan vendor:publish --tag="filament-knowledge-base-translations"
```

And the config file:

```bash
php artisan vendor:publish --tag="filament-knowledge-base-config"
```

Neither is required for the package to work.

Next up, [create your knowledge base panel](panel/01-setup.md).
