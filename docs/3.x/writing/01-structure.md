---
title: Structure
---

# Structure

By default, your markdown files live in `docs/<panel-id>` in the root of your project (configurable in the [plugin setup](../panel/01-setup.md)). Inside that directory, there is one folder per locale, and inside the locale folder your actual documentation:

- `docs/`
    - `<panel-id>/`
        - `en/`
            - `getting-started.md`
            - `users/`
                - `introduction.md`
                - `roles.md`
                - `roles/`
                    - `user.md`
                    - `admin.md`
        - `de/`
            - `getting-started.md`
            - ...

The knowledge base mirrors this tree in the panel navigation.

> [!IMPORTANT]
> The locale folder is required, even if you only support one language. Files placed directly in `docs/<panel-id>` are ignored.

## Nesting rules

The structure follows a few strict rules, because filament's navigation does not support arbitrarily deep nesting:

1. You can nest **at most 3 levels deep** (as in the example above).
2. In the top level (the locale directory), you can freely mix markdown files and directories.
3. From the second level down, every directory needs a sibling markdown file of the same name (`roles/` needs a `roles.md` next to it). That file acts as the parent item of everything inside the directory.

We try to throw meaningful exceptions whenever the structure is wrong, so if the panel crashes after adding a file, the message should point you to the problem.

## Groups and parents

The relationship between items is resolved from the directory structure:

- A directory with a sibling `<name>.md` of type `group` becomes a **group** in the navigation, with the files inside as its items.
- A directory with a sibling `<name>.md` that is a regular documentation file becomes a **parent item** with the files inside as children.

A group config file only consists of front matter:

```md
---
type: group
title: Advanced
icon: heroicon-o-academic-cap
---
```

## Generating files

You don't have to create the files by hand. The `docs:make` command walks you through it:

```bash
php artisan docs:make
```

You can also pass everything up front, including one or more locales:

```bash
php artisan docs:make knowledge-base documentation prologue.getting-started --locale=en --locale=de
```

If you don't pass any locale, the file is created for every locale folder that already exists in your docs directory.

## Caching

The parsed markdown is cached, so you don't pay the rendering cost on every request. During development, clear it whenever you don't see your changes:

```bash
php artisan cache:clear
```

More on this in [cache](../advanced/02-cache.md).
