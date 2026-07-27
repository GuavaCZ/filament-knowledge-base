---
title: Upgrading
---

# Upgrading

This guide covers the upgrade from 2.x to 3.x.

Version 3.x exists for filament 5 and requires PHP 8.2 and Laravel 12 or newer. There are no changes to the public API of the package, so in most cases you only need to upgrade filament itself and bump the constraint.

## Upgrade filament

Please follow the [filament upgrade guide](https://filamentphp.com/docs/5.x/upgrade-guide) first. If your panels don't run on filament 5 yet, the knowledge base won't work either.

## Bump the constraint

```bash
composer require guava/filament-knowledge-base:"^3.0"
```

Then republish the assets, since the built JS changed:

```bash
php artisan filament:assets
```

## Check your theme

The source paths in your **theme.css** are unchanged:

```css
@plugin "@tailwindcss/typography";
@source '../../../../vendor/guava/filament-knowledge-base/src/**/*';
@source '../../../../vendor/guava/filament-knowledge-base/resources/views/**/*';
```

If you never added them, please read the [installation](02-installation.md) page, as a custom theme is required.

## Upgrading from 1.x

If you are still on 1.x, please follow the [2.x upgrade guide](../2.x/06-upgrading.md) first, since version 2 restructured the package quite a bit (multiple knowledge base panels, directory based groups, phiki instead of shiki).

## Found an issue?

If you find a step that is missing here, please open a PR and modify this file. We will review it and merge it.
