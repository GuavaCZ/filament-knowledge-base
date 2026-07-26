# Upgrade guide

There are a few major changes in filament knowledge base 2. In this guide we try to cover all changes that need to be done in order to upgrade from 1.x to 2.x.

## Multiple knowledge base panels

A major change in filament knowledge base 2 is that you can now have multiple knowledge base panels. This means that you can have a separate panel for each of your knowledge bases.

### Change old `KnowledgeBasePlugin` to `KnowledgeBaseCompanionPlugin` imports

This means you will have a plugin `KnowledgeBasePlugin` in your **regular filament panels**. Please change this import to the new plugin `KnowledgeBaseCompanionPlugin`, which is specifically there to bridge the gap between regular filament panels and knowledge base panels. 

### Knowledge base configuration

You used to configure your single knowledge base panel using the `KnowledgeBasePanel::configureUsing` method.

The `KnowledgeBasePanel` class has been removed completely in favor of custom filament panels.

Please generate a new filament panel for your knowledge base using `php artisan make:filament-panel` and following the instructions. For detailed information on how to do this, please see the [filament documentation here](https://filamentphp.com/docs/3.x/panels/configuration#creating-a-new-panel).

When you are done, also create a custom theme for your knowledge base panel. Similarly, if you don't know how to do this, follow the [filament documentation here](https://filamentphp.com/docs/3.x/panels/themes#creating-a-custom-theme).

In the newly generated theme for your knowledge base, add the following to the `content` option of your `tailwind.config.js`:

```js
module.exports = {
    content: [
        // ...
        './vendor/guava/filament-knowledge-base/src/**/*.php',
        './vendor/guava/filament-knowledge-base/resources/**/*.blade.php',
    ],
};
```

### Move your markdown docs

As we now support multiple knowledge bases, you need to create a new directory inside your `docs` folder for each knowledge base. The name of the folder needs to correspond with the ID of the panel, so if your knowledge base panel ID is `kb`, create a folder `/docs/kb` and move all your documentation files (including the directories for each locale) inside this folder.

An example of your new directory structure culd look like this:

```
/docs
  /kb
    /en
      /01-intro.md
    /de
      /01-intro.md
```

## Markdown files

Additionally, the way relationships are defined in markdown files has changed.

Groups and parent no longer need to be defined using a `group` or `parent` key in the front matter. Instead, they are automatically resolved from the directory structure.

### Parent
For parent files, you should not have to do anything. Just make sure that for each parent item you have a directory with it's children.

For example, if `parent.md` is your parent, all it's child documentation belong to `parent/` directory, such as:
```
/docs/kb/en/parent.md
/docs/kb/en/parent/child-1.md
/docs/kb/en/parent/child-2.md
```

### Group
For groups, you need to create a markdown file for each group that serves as it's config.

Then all child items of the group belong to a directory of the same name as the config file.

So, if you had a group `advanced` and two items in it `item-1` and `item-2`, your new directory structure will be:

```
/docs/kb/en/advanced.md
/docs/kb/en/advanced/item-1.md
/docs/kb/en/advanced/item-2.md
```

The config file of the group (in this example `advanced.md`) should look like this:

```markdown
---
type: group // This is how you define this item to be a group
title: 'Advanced' // This is the title of the group, if ommited the file name will be used
icon: 'heroicon-o-user' // This is the icon that will be used for this group, it's optional
---
```

## Syntax highlighting

We replaced `shiki.js` by a PHP based solution called `phiki`. Phiki is a PHP port of `shiki.js` and is much easier to work with in a PHP environment.

Syntax highlighting is now enabled.

You can also safely remove your NPM `shiki.js` and `tm-grammar` dependencies and your composer dependency `shikiphp`.

## Documentation Styles

We removed all our custom styling and replaced it with tailwinds `prose` utility class. This means your documentation will look a bit different, but it will be much easier to customize and extend.

Please make sure to check your documentation and adjust the styling if needed.

## Model renamed

If you work with the Documentation model somewhere in your code, please note that the model name is now `FlatfileNode`, as it now represents more than just your documentation files.

Please adjust your imports and references accordingly.

## Config

Configuration option `filament-knowledge-base.model` has been renamed to `filament-knowledge-base.flatfile-model`.

Configuration options `filament-knowledge-base.panel` and `filament-knowledge-base.docs-path` have been completely removed. 

Please adjust your config file accordingly.

## Found an issue?

If you find an undocumented step that needs to be done to upgrade, please open a PR and modify this file with the necessary changes. We will review it and merge it.
