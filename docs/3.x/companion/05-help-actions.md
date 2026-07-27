---
title: Help actions
---

# Help actions

The `HelpAction` links a single form field, component or any other action slot to a documentation page — or, more commonly, to a small partial of one.

For example, a "What is a slug?" hint on a field:

```php
use Guava\FilamentKnowledgeBase\Actions\HelpAction;

TextInput::make('slug')
    ->hintAction(
        HelpAction::forDocumentable('projects.creating-projects.slug')
            ->label('What is a slug?')
    );
```

When [modal previews](03-modal-previews.md) are enabled, the action opens the documentation in a modal, otherwise it links to the full page.

## Partials

Help actions shine together with [includes](../writing/03-markdown.md#including-other-files): extract a small snippet (like the slug explanation) into its own markdown file with `active: false`, include it in your main documentation, and point the `HelpAction` at the snippet. The user gets a small, focused help modal, and your knowledge base has no duplicated content.
