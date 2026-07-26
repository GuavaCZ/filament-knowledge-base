<?php

namespace Guava\FilamentKnowledgeBase\Filament\Pages;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Panel;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Guava\FilamentKnowledgeBase\Enums\NodeType;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Guava\FilamentKnowledgeBase\Filament\Resources\DocumentationResource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
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

        return $this->getRecord()->getBreadcrumbs();
    }

    /**
     * The record is always a Documentable model, but the inherited property is
     * only typed as Model|int|string.
     *
     * @return Model&Documentable
     */
    public function getRecord(): Model
    {
        $record = parent::getRecord();

        assert($record instanceof Documentable);

        return $record;
    }

    public function mount(int | string | null $record = null): void
    {
        // The companion plugin's sidebar button links to the panel root (no record),
        // so redirect to the first root node instead of failing to resolve one.
        if (blank($record)) {
            $node = KnowledgeBase::model()::query()
                ->where('panel_id', KnowledgeBase::panel()->getId())
                ->whereNull('parent_id')
                ->orderBy('order')
                ->first()
            ;

            abort_unless((bool) $node, 404);

            // The page still renders once before the redirect is followed, so the
            // record must be initialized like in the group redirect below.
            $this->record = $node;
            $this->redirect($node->getUrl());

            return;
        }

        parent::mount($record);

        if ($this->getRecord()->getType() === NodeType::Group) {
            if ($child = $this->getRecord()->children()->first()) {
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
        foreach ($this->getRecord()->getAnchors() as $anchor => $label) {
            $pages[] = NavigationItem::make($label)
                ->url("#$anchor")
            ;
        }

        return $pages;
    }

    public function getTitle(): string | Htmlable
    {
        return $this->getRecord()->getTitle() ?? '';
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
