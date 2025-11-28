<?php

namespace Guava\FilamentKnowledgeBase\Actions;

use Filament\Actions\Action;
use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Illuminate\Support\HtmlString;

class HelpAction extends Action
{
    protected function setUp(): void {}

    public function generic(): HelpAction
    {
        return $this->label(__('filament-knowledge-base::translations.help'))
            ->icon('heroicon-o-question-mark-circle')
            ->iconSize('lg')
            ->color('gray')
            ->button()
        ;
    }

    public static function forDocumentable(Documentable | string $documentable, ?string $panelId = null): HelpAction
    {
        $documentable = KnowledgeBase::documentable($documentable, $panelId);
        return static::make("help.{$documentable->getId()}")
            ->label($documentable->getTitle())
            ->icon($documentable->getIcon())
            ->when(
                KnowledgeBase::companion()->hasModalPreviews(),
                fn(HelpAction $action) => $action
                    ->modal()
                    ->modalContent(function () use ($documentable) {
                        $content = data_get($documentable->getData(), 'content', '');
                        $body = new HtmlString("<div class='prose dark:prose-invert'>{$content}</div>");
                        return $body;
                    })
                    ->modalCancelActionLabel(__('filament-knowledge-base::translations.close'))
                    ->modalSubmitAction(false)
                    ->when(
                        KnowledgeBase::companion()->hasSlideOverPreviews(),
                        fn(HelpAction $action) => $action->slideOver()
                    ),
                fn(HelpAction $action) => $action->url($documentable->getUrl())
            )
        ;
    }
}
