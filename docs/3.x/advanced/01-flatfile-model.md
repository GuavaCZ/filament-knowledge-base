---
title: Flatfile model
---

# Flatfile model

Under the hood, your markdown files are turned into rows of the `FlatfileNode` model, powered by [sushi](https://github.com/calebporzio/sushi). That means every documentation page behaves almost like a regular eloquent model.

## Querying

Use the `KnowledgeBase` facade to get the model class:

```php
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;

// Find a specific page
KnowledgeBase::model()::find('knowledge-base.users.introduction');

// Query pages
KnowledgeBase::model()::query()->where('title', 'Some title')->get();
```

> [!NOTE]
> Model IDs are prefixed with the panel ID of the knowledge base panel they belong to, so the page `users/introduction.md` of the `knowledge-base` panel has the ID `knowledge-base.users.introduction`.

## Other facade helpers

```php
// The knowledge base panel (resolved from the current panel)
KnowledgeBase::panel();

// The KnowledgeBasePlugin / KnowledgeBaseCompanionPlugin instance of a panel
KnowledgeBase::plugin();
KnowledgeBase::companion();

// The rendered HTML of a documentation page
KnowledgeBase::markdown('users.introduction');
```

## Global search

The knowledge base panel supports filament's global search out of the box, looking through the `title` and the `content` of your markdown files.

## Custom model

If you need to change the behavior of the model, extend `FlatfileNode` and point the config to your class:

```php
// config/filament-knowledge-base.php
return [
    'flatfile-model' => App\Models\MyFlatfileNode::class,

    // ...
];
```
