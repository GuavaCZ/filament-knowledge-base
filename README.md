<img src="https://github.com/GuavaCZ/filament-knowledge-base/raw/main/.github/banner.png" alt="filament-knowledge-base Banner" class="filament-hidden">

# Knowledge Base for your filament panels

[![Latest Version on Packagist](https://img.shields.io/packagist/v/guava/filament-knowledge-base.svg?style=flat-square)](https://packagist.org/packages/guava/filament-knowledge-base)
[![Total Downloads](https://img.shields.io/packagist/dt/guava/filament-knowledge-base.svg?style=flat-square)](https://packagist.org/packages/guava/filament-knowledge-base)

This plugin adds a markdown powered knowledge base to your filament app. You write markdown files, and the package turns them into a fully navigable documentation panel with a sidebar, breadcrumbs, a table of contents and global search.

A companion plugin integrates the knowledge base into your regular panels: a help menu on your resources and pages, documentation previews in modals and a link to the knowledge base in the sidebar.

![Showcase](https://github.com/GuavaCZ/filament-knowledge-base/raw/main/docs/3.x/_assets/screenshot_01.jpeg)

## Documentation

The full documentation is available at [guava.cz](https://guava.cz/developers/packages/filament-knowledge-base).

## Version compatibility

| Filament version | Plugin version |
|------------------|:--------------:|
| 3.x              |      1.x       |
| 4.x              |      2.x       |
| 5.x              |      3.x       |

For older filament versions, please check the branch of the respective version.

## Installation

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

Finally, make sure you have a **custom filament theme** (read [here](https://filamentphp.com/docs/5.x/styling/overview#creating-a-custom-theme) how to create one) and add the following to your **theme.css** file so the CSS is properly built:

```css
@plugin "@tailwindcss/typography";
@source '../../../../vendor/guava/filament-knowledge-base/src/**/*';
@source '../../../../vendor/guava/filament-knowledge-base/resources/views/**/*';
```

For the remaining setup steps, please see the [installation docs](https://guava.cz/developers/packages/filament-knowledge-base/3.x/installation).

## Usage

The knowledge base needs its own panel. Register the `KnowledgeBasePlugin` on it:

```php
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;

$panel->plugin(KnowledgeBasePlugin::make());
```

And the `KnowledgeBaseCompanionPlugin` on your regular panel(s):

```php
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBaseCompanionPlugin;

$panel->plugin(KnowledgeBaseCompanionPlugin::make()
    ->knowledgeBasePanelId('knowledge-base')
);
```

Then create your first documentation page:

```bash
php artisan docs:make
```

Everything else, including the markdown features, modal previews, help menus and help actions, is covered in the [documentation]([docs/3.x/01-introduction.md](https://guava.cz/developers/packages/filament-knowledge-base)).

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Lukas Frey](https://github.com/GuavaCZ)
- [All Contributors](../../contributors)
- [Phiki](https://github.com/phikiphp/phiki) - syntax highlighting
- [league/commonmark](https://commonmark.thephpleague.com/) - markdown parsing
- Spatie - Our package skeleton is a modified version of [Spatie's Package Tools](https://github.com/spatie/laravel-package-tools)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
