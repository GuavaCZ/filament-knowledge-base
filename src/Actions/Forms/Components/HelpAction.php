<?php

namespace Guava\FilamentKnowledgeBase\Actions\Forms\Components;

use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Illuminate\Support\HtmlString;

class HelpAction extends Action
{
    //    protected Documentable | string $documentation;
    //
    //    public function documentation(Documentable | string $documentation): static
    //    {
    //        $this->documentation = $documentation;
    //
    //        return $this;
    //    }
    //
    //    public function getDocumentation(): Documentable | string
    //    {
    //        return $this->evaluate($this->documentation);
    //    }

    public function setUp(): void
    {
        $this
            ->modalContent(function () {
                return new HtmlString($this->getDocumentation()->getSimpleHtml());
            })
        ;
    }

    public static function forDocumentable(Documentable $documentable): static
    {
        return static::make("help.{$documentable->getId()}")
            ->label($documentable->getTitle())
//            ->icon($documentable->getIcon())
            ->icon('heroicon-o-question-mark-circle')
            ->when(
                Filament::getPlugin('guava::filament-knowledge-base')->hasModalPreviews(),
                fn (HelpAction $action) => $action
                    ->modalContent(fn () => new HtmlString($documentable->getSimpleHtml()))
                    ->modalHeading($documentable->getTitle())
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament-knowledge-base::translations.close'))
                    ->when(
                        Filament::getPlugin('guava::filament-knowledge-base')->hasSlideOverPreviews(),
                        fn (HelpAction $action) => $action->slideOver()
                    ),
                fn (HelpAction $action) => $action->url('#'.$documentable->getId())//TODO: Change to real URL
            )
        ;
    }
}
