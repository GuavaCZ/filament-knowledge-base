# Knowledge Base

Filament Knowledge Base allows you to create markdown powered knowledge base panels into your filament app.

## Installation
You can install the package via composer:

```bash
composer require guava/filament-knowledge-base:"^2.0"
```

Next, install `tailwind/typography` if you don't have it already, since we use `prose` to style the markdown output:
```bash
npm install -D @tailwindcss/typography
```

## Introduction
Unlike most other filament packages, this package provide two separate `plugins` instead of a single one. 

The first plugin, `KnowledgeBasePlugin` is for your panel that you want to turn into a knowledge base panel(s).

The second plugin, `KnowledgeBaseCompanionPlugin` is for your regular filament panel to add supporting features and integrations with your knowledge base panel(s). For example, this let's you access snippets of your documentation pages via modals right inside your regular panels.

To learn more about each plugin, please refer to their own documentation pages.

## Setup
If you don't have a separate panel yet for your knowledge base, please create one using the built in filament command:
```bash
php artisan filament:panel
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
> If you don't have one, please refer to the filament documentation on how to create a custom theme.

And lastly, add the following to your custom filament theme to correctly build the CSS and use the required tailwind plugins:
```css
@plugin "@tailwindcss/typography";
@source '../../../../vendor/guava/filament-knowledge-base/src/**/*';
@source '../../../../vendor/guava/filament-knowledge-base/resources/view/**/*';
```

> [!IMPORTANT]
> It is **important** that both your filament knowledge base panel and your regular panel(s) use a custom theme with these source paths. It's up to you if you want to use the same theme for all panels or different themes for each.

## Knowledge Base content
By default, all your documentation files should live in the `docs/<panel-id>` directory in the root of your project. You can change this in your knowledge base plugin configuration.

The directory structure needs to strictly adhere to the following structure:
- `/docs`
  - `/<panel-id>`
    - `/<locale>`
      - `/<markdown-files-here, max 3 levels>`
  
For example:
- `/docs`
  - `/knowledge-base`
      - `/en`
          - `intro.md`
          - `/users`
              - `/intro.md`
              - `/roles.md`
              - `/roles`
                  - `user.md`
                  - `admin.md`
      - `/de`
          - `intro.md`
          - `/users`
            - `/intro.md`
            - `/roles.md`
            - `/roles`
                - `user.md`
                - `admin.md`

The directory structure is fairly limited due to the navigation system of filament and thus is very error prone, although we try to throw meaningful exceptions anytime the structure is wrong.

If you are unsure, make sure these rules are always met:
1. You are nested MAXIMALLY 3 levels deep (as in the example above)
2. In the top level directory (the language directory), you can have any markdown files and directories.
3. In the second level directory, you must have an explicit `<dir>.md` file for each directory. This is because filament does not support deeply nested groups and thus it needs to be a `Parent Item`. In the example above, you can see that for the `roles` directory there also exists a `roles.md` documentation item which acts as the `Parent Item`.

### Creating documentation pages
Right now, you need to manually create the files inside the correct structure.

Try creating your first documentation page. Open your terminal in the root directory of your project and enter:
```bash
mkdir -p ./docs/knowledge-base/en && touch ./docs/knowledge-base/en/intro.md
```

Now open the file in your favorite editor and paste the following example:
```md
---
title: Introduction
---
# Introduction
This is my first documentation page
```

If you visit your regular filament panel, you should now see a button at the bottom of your sidebar that will lead you to your knowledge base panel with your first documentation page!

To learn more about the customization possibilities, please refer to the pages of each individual plugin:
