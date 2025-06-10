<?php

namespace Guava\FilamentKnowledgeBase\Filament\Pages;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Panel;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentKnowledgeBase\Enums\NodeType;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Guava\FilamentKnowledgeBase\Filament\Resources\DocumentationResource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Livewire\Attributes\On;

class ViewDocumentation extends ViewRecord
{
    protected static string $resource = DocumentationResource::class;

    //    public Model $record;
    protected string $view = 'filament-knowledge-base::documentation';

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return KnowledgeBase::plugin()->getTableOfContentsPosition()->toSubNavigationPosition();
    }

    public function getBreadcrumbs(): array
    {
        if (KnowledgeBase::plugin()->shouldDisableBreadcrumbs()) {
            return [];
        }

        return $this->record->getBreadcrumbs();
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if ($this->record->getType() === NodeType::Group) {
            if ($child = $this->record->children()?->first()) {
                $this->redirect($child->getUrl());
            } else {
                $this->redirect(KnowledgeBase::panel()->getUrl());
            }
        }
    }

    public static function route(string $path): PageRegistration
    {
        return new PageRegistration(
            page: static::class,
            route: fn (Panel $panel): Route => RouteFacade::get($path, static::class)
                ->middleware(static::getRouteMiddleware($panel))
                ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
                ->where('record', '.*'),
        );
    }

    public function getSubNavigation(): array
    {
        if (KnowledgeBase::plugin()->shouldDisableTableOfContents()) {
            return [];
        }

        $pages = [];
        foreach ($this->record->getAnchors() as $anchor => $label) {
            $pages[] = NavigationItem::make($label)
                ->url("#$anchor")
            ;
        }

        return $pages;
    }

    public function getTitle(): string | Htmlable
    {
        return $this->record->getTitle();
    }

    #[On('documentation.anchor.copy')]
    public function copyAnchorToClipboard(string $url): void
    {
        $this->js(<<<JS
        if (navigator.clipboard) {
            await navigator.clipboard.writeText('$url').then(() => {
                (new FilamentNotification()).title(filamentKnowledgeBaseTranslations.urlCopied)
                .success()
                .send();
            }).catch((err) => {
                console.error('Failed to copy text: ', err);
            });
        }
JS);
    }
}
