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

    public static function forDocumentable(Documentable | string $documentable): HelpAction
    {
        $documentable = KnowledgeBase::documentable($documentable);

        return static::make("help.{$documentable->getId()}")
            ->label($documentable->getTitle())
            ->icon($documentable->getIcon())
            ->when(
                KnowledgeBase::companion()->hasModalPreviews(),
                fn (HelpAction $action) => $action
                    ->modal()
                    ->modalContent(new HtmlString('test'))
                    ->action(fn() => dd('test'))
//                    ->alpineClickHandler('$dispatch(\"open-modal\", {id: "' . $documentable->getId() . '"})')
                    ->when(
                        KnowledgeBase::companion()->hasSlideOverPreviews(),
                        fn (HelpAction $action) => $action->slideOver()
                    ),
                fn (HelpAction $action) => $action->url($documentable->getUrl())
            )
        ;
    }
}
