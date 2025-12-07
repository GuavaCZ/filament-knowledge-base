<?php

namespace Guava\FilamentKnowledgeBase\Models;

use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Guava\FilamentKnowledgeBase\Enums\NodeType;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Guava\FilamentKnowledgeBase\Filament\Pages\ViewDocumentation;
use Guava\FilamentKnowledgeBase\KnowledgeBaseRegistry;
use Guava\FilamentKnowledgeBase\Markdown\MarkdownRenderer;
use Guava\FilamentKnowledgeBase\Support\FlatfileParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;
use League\CommonMark\Output\RenderedContentInterface;
use Sushi\Sushi;

class FlatfileNode extends Model implements Documentable
{
    use Sushi;

    public $incrementing = false;

    protected $schema = [
        'id' => 'string',
        'slug' => 'string',
        'type' => 'string',
        'path' => 'string',
        'icon' => 'string',
        'title' => 'string',
        'order' => 'integer',
        'active' => 'boolean',
        'data' => 'json',
        'parent_id' => 'string',
        'panel_id' => 'string',
    ];

    protected function casts(): array
    {
        return [
            'type' => NodeType::class,
            'data' => 'array',
            'active' => 'boolean',
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function parent(): ?FlatfileNode
    {
        return static::query()
            ->where('panel_id', $this->getPanelId())
            ->where('id', $this->parent_id)
            ->first()
        ;
    }

    public function children(): Collection
    {
        return static::query()
            ->where('panel_id', $this->getPanelId())
            ->where('parent_id', $this->getId())
            ->get()
        ;
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = collect();
        $record = $this;

        do {
            $breadcrumbs->put($record->getUrl(), $record->getTitle());
        } while ($record = $record->parent());

        return $breadcrumbs
            ->put(KnowledgeBase::panel()->getUrl(), __('filament-knowledge-base::translations.knowledge-base'))
            ->reverse()
            ->toArray()
        ;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getOrder(): int
    {
        return $this->order ?? 0;
    }

    public function getIcon(): ?string
    {
        return $this->icon ?? $this->getDefaultIcon();
    }

    public function getType(): NodeType
    {
        return $this->type;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getHtml(): RenderedContentInterface
    {
        $converter = app(MarkdownRenderer::class);

        return $converter->convert(file_get_contents($this->path));
    }

    public function getSimpleHtml(): RenderedContentInterface
    {
        $converter = app(MarkdownRenderer::class)->minimal();

        return $converter->convert(file_get_contents($this->path));
    }

    public function getUrl(): string
    {
        return match ($this->getType()) {
            NodeType::Link => $this->getData()['url'],
            default => ViewDocumentation::getUrl(parameters: [
                'record' => $this,
            ], panel: $this->panel_id)
        };
    }

    public function getPanelId(): string
    {
        return $this->panel_id;
    }

    public function getLocale(): string
    {
        return App::getLocale();
    }

    public function getFallbackLocale(): string
    {
        return App::getFallbackLocale();
    }

    public function toNavigationItem(): NavigationItem
    {
        if ($this->type === NodeType::Group) {
            throw new \Exception('Cannot convert a group to a navigation item');
        }

        $item = NavigationItem::make($this->getTitle())
            ->icon($this->getIcon())
            ->sort($this->getOrder())
            ->url($this->getUrl())
            ->isActiveWhen(fn () => url()->current() === $this->getUrl())
        ;

        if ($parent = $this->parent()) {
            match ($parent->getType()) {
                NodeType::Group => $item->group($parent->getTitle()),
                default => $item
                    ->parentItem($parent->getTitle())
                    ->group($parent->parent()?->getTitle()),
            };
        }

        return $item;
    }

    public function toNavigationGroup(): NavigationGroup
    {
        if ($this->type !== NodeType::Group) {
            throw new \Exception('Cannot convert a document to a navigation group');
        }

        $canHaveIcon = $this
            ->children()->where(fn (FlatfileNode $child) => $child->children()->isNotEmpty())
            ->isEmpty()
        ;

        return NavigationGroup::make($this->getTitle())
            ->icon($canHaveIcon ? $this->getIcon() : null)
        ;
    }

    public function getAnchors(): array
    {
        $walker = $this->getHtml()->getDocument()->walker();

        $anchors = [];
        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($event->isEntering() && $node instanceof HeadingPermalink) {
                $slug = $node->getSlug();
                $next = $node->next();

                if (! method_exists($next, 'getLiteral')) {
                    continue;
                }

                $anchors[$slug] = $next->getLiteral();
            }
        }

        return $anchors;
    }

    public function getRows(): array
    {
        $rows = collect();

        $paths = app(KnowledgeBaseRegistry::class)->getDocsPaths();

        foreach ($paths as $panelId => $path) {
            // Get localized docs path
            $localizedPath = str($path)
                ->rtrim(DIRECTORY_SEPARATOR)
                ->append(DIRECTORY_SEPARATOR)
                ->append($this->getLocale())
            ;

            // Get fallback locale docs path
            if (! File::exists($localizedPath)) {
                $localizedPath = str($path)
                    ->rtrim(DIRECTORY_SEPARATOR)
                    ->append(DIRECTORY_SEPARATOR)
                    ->append($this->getFallbackLocale())
                ;
            }

            // No docs present
            if (! File::exists($localizedPath)) {
                continue;
            }

            $rows->push(...FlatfileParser::make($panelId, $localizedPath)->get());
        }

        return $rows->toArray();
    }

    public function scopeType(Builder $query, NodeType ...$types): Builder
    {
        return $query->where(
            fn (Builder $query) => collect($types)->each(fn (NodeType $type) => $query->orWhere('type', $type))
        );
    }

    public function resolveRouteBindingQuery($query, $value, $field = null): \Illuminate\Contracts\Database\Eloquent\Builder
    {
        return parent::resolveRouteBindingQuery($query, $value, $field)
            ->where('panel_id', KnowledgeBase::panel()->getId())
        ;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function getDefaultIcon(): ?string
    {
        $icons = config('filament-knowledge-base.icons');

        return match ($this->getType()) {
            NodeType::Documentation => Arr::get($icons, NodeType::Documentation->value, 'heroicon-o-document'),
            NodeType::Link => Arr::get($icons, NodeType::Link->value, 'heroicon-o-link'),
            NodeType::Group => Arr::get($icons, NodeType::Group->value),
        };
    }
}
