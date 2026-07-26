# Markdown files

## Introduction

The primary way to fill your knowledge base with documentation files is by writing markdown files.

Each markdown file consists of two distinct parts: the **front matter** and the **content**.

The front matter is a YAML block enclosed by three dashes (`---`), that contains metadata and settings of the documentation, such as the *title, icon, and order*. 

The content is the actual markdown text that will be displayed in the knowledge panel.

The markdown content is parsed using [CommonMark](https://commonmark.thephpleague.com/) and supports basic markdown syntax, including headings, lists, links, images, basic text formatting and code blocks. Optionally, you can also enable syntax highlighting with a few extra installation steps.

## Structure

By default, your markdown files go to the `docs` directory in the root of your project. This can be customized to your liking.

Each panel has its own folder named after the panel ID and within that a folder for each locale. For example, if your knowledge base panel ID is `my-knowledge-base`, the markdown files will be located in `docs/my-knowledge-base/en` for English and `docs/my-knowledge-base/es` for Spanish. 

You can also nest your documentation files up to **three levels deep** within this directory to organize your markdown files. The knowledge base will mimic the same tree structure in the knowledge panel navigation.

#### Example directory stucture
- `docs/`
    - `<your-panel-id>/`
        - `en/`
            - `01-getting-started.md`
            - `02-faq.md`
            - `users/`
                - `01-introduction.md`
                - `02-creating-a-user.md`
        - `es/`
            - `01-getting-started.md`
            - `02-faq.md`
            - `users/`
                - `01-introduction.md`
                - `02-creating-a-user.md`

#### Example file structure
```
---
title: Getting Started
icon: heroicon-o-home
---
# Getting Started

**Lorem ipsum dolor** sit amet, consectetur adipiscing elit...
```

## Front Matter

The front matter is a YAML block that contains metadata and settings of the documentation. It is placed at the top of the markdown file, enclosed by three dashes (`---`) on each side.

```
---
Front matter goes here...
---
```

### Available options

We currently support the following options in the front matter:

#### Title
This option allows you to customize the title of the documentation file. The title is displayed in the knowledge panel navigation, in the header of the documentation page and in the breadcrumbs.

If you don't specify a title, a prettified version of the file name will be used as the title. For example, if your file is named `getting-started.md`, the title will be `Getting Started`.

```yaml
title: Getting Started
```

#### Icon
This option allows you to customize the icon of the documentation file. The icon is displayed in the knowledge panel navigation.

You are not limited to only heroicons, you can use any icon from any Blade UI icons pack that you have installed.

If you don't specify an icon, the default icon `heroicon-o-document` will be used.

```yaml
icon: heroicon-o-user
```

#### Order
This option allows you to override the order of the documentation file in the knowledge panel navigation. The order is a number that determines the position of the file in the navigation.

A lower number means a higher position in the navigation.

If you don't specify an order, the default ordering is **alphabetical order of the file names.** 

This is useful if you want to order your files using numerical ordering, such as: `01-getting-started`, `02-creating-a-user` and so on.

```yaml
order: 3
```

#### Active
This option allows you to hide the documentation file. This is useful if you want to hide the documentation from the navigation and prevent users from viewing it.

However, keep in mind the documentation file **can still be viewed in your regular filament panels in modal previews!**

```yaml
active: false
```

#### URL
> [!NOTE] This option is only applicable for the `link` type.

This option allows you to specify a URL for the documentation file. It will be opened when the user clicks on the documentation item in the navigation.

```yaml
url: https://example.com
```

#### Type
This option allows you to determine the type of the documentation file.

There are currently the following types available:
- `document`: This is the primary type and is used for the majority of documentation files. This is where you write your markdown content. Documentation files of this type can also be used as parents by other documentation types. This type can be omitted, as it is the default type.
- `group`: This type is used to group multiple documentation files together. The group will be displayed in the knowledge panel navigation and the documentation files within the group will be displayed as sub-items of the group.
- `link`: This type is used to display a link to an external URL. The link will be displayed in the knowledge panel navigation and will open the URL in a new tab.

```yaml
type: group
```

## Content

Anything that comes after the front matter is considered content. The content is parsed using [CommonMark](https://commonmark.thephpleague.com/).

```
---
Front matter goes here...
---
Content goes here...
```

### Formatting

Basic markdown syntax is supported, including headings, lists, links, images, basic text formatting and code blocks.

To learn more about the available formatting options, please refer to the [CommonMark website](https://commonmark.org/) and [CommonMark PHP documentation](https://commonmark.thephpleague.com/).

### Built-in CommonMark extensions

By default, the following extensions are enabled:

 - CommonMarkCoreExtension
 - DefaultAttributesExtension
 - AttributesExtension
 - FrontMatterExtension
 - MarkerExtension
 - TableExtension

### Custom options

Knowledge base comes with a few custom options that you can use in your markdown files.

#### Markers
You can mark important parts of your documentation using the marker syntax. This will render a colored box around your text in the primary color of your knowledge base panel.

```markdown
In this example, ==this part== of the text is going to be marked.
```

#### Includes

To allow you to reuse content across multiple Markdown files, you can use the `include` directive. This directive allows you to include the content of another markdown file in your current file.

This is especially useful for `partials`, which are small reusable pieces of content that you can include in multiple Markdown files. 

This becomes extremely useful when integrating with your regular filament panel, as it allows you to display these partials in modals when documenting small parts of your application.

```markdown
@include(<id-of-documentation-file>)
```

The ID of your documentation file is the **dot-notation representation of the relative path to the file**, starting from the knowledge bases `docs` directory.

For example, consider the following documentation file: `/docs/my-knowledge-base/en/users/advanced/customization.md`, the ID would be `users.advanced.customization`.

### Vite assets
You can include Vite assets (images) in your markdown files using the regular markdown image syntax. For the path of the image, use the same path you would pass to your `Vite::asset()` method.

For example, if your image is located in `/resources/img/logo.png`, you can include it in your markdown file like this:

```markdown
![Logo](/resources/img/logo.png)
```

We will automatically wrap all relative paths in the `Vite::asset()` method. That means, both `npm run dev` will work on localhost as well as `npm run build` on production.

### Syntax highlighting
Syntax highlighting is enabled by default for all code blocks in your markdown files.

Syntax highlighting is provided by [PhikiPHP](https://github.com/phikiphp/phiki).

[//]: # (#### Variables)

[//]: # ()
[//]: # (When accessing the documentation file from within your regular filament panel, you sometimes have access to the `$record` property, for example in resource pages or modals. )

[//]: # ()
[//]: # (In this case, it is possible to access properties of the `record` using the `@var&#40;&#41;` directive.)

[//]: # ()
[//]: # (```markdown)

[//]: # (@var&#40;<property>&#41;)

[//]: # (```)

[//]: # ()
[//]: # (For example, if you have a `User` resource and you want to display the name of the user in your documentation file, you can use the following syntax:)

[//]: # ()
[//]: # (```markdown)

[//]: # (@var&#40;name&#41;)

[//]: # (```)

[//]: # ()
[//]: # (This property can currently be only used in the context of **edit** and **view** resource pages, and only if the companion plugin is registered in the panel.)
