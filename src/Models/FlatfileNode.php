<?php

namespace Guava\FilamentKnowledgeBase\Models;

use Arr;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Guava\FilamentKnowledgeBase\Enums\NodeType;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Guava\FilamentKnowledgeBase\Filament\Pages\ViewDocumentation;
use Guava\FilamentKnowledgeBase\KnowledgeBaseRegistry;
use Guava\FilamentKnowledgeBase\Markdown\MarkdownRenderer;
use Guava\FilamentKnowledgeBase\Support\FlatfileParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;
use League\CommonMark\Output\RenderedContentInterface;
use Sushi\Sushi;

class FlatfileNode extends Model implements Documentable
{
    use Sushi;

    protected $schema = [
        'id' => 'string',
        'slug' => 'string',
        'type' => 'string',
        'path' => 'string',
        'title' => 'string',
        'icon' => 'string',
        'data' => 'json',
        'parent_id' => 'string',
        'order' => 'integer',
        'panel_id' => 'string',
    ];

    protected function casts(): array
    {
        return [
            'type' => NodeType::class,
            'data' => 'array',
        ];
    }

    public $incrementing = false;

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

    //    public function getPanel(): Panel
    //    {
    //        return Filament::getPanel($this->panel_id);
    //    }
    //
    public function getLocale(): string
    {
        return App::getLocale();
    }

    //
    public function getFallbackLocale(): string
    {
        return App::getFallbackLocale();
    }

    //
    //    public function getRows(): array
    //    {
    //        $rows = collect();
    //
    //        $paths = app(KnowledgeBaseRegistry::class)->getDocsPaths();
    //
    //        foreach ($paths as $panelId => $path) {
    //            // Get localized docs path
    //            $localizedPath = str($path)
    //                ->rtrim(DIRECTORY_SEPARATOR)
    //                ->append(DIRECTORY_SEPARATOR)
    //                ->append($this->getLocale())
    //            ;
    //
    //            // Get fallback locale docs path
    //            if (! File::exists($localizedPath)) {
    //                $localizedPath = str($path)
    //                    ->rtrim(DIRECTORY_SEPARATOR)
    //                    ->append(DIRECTORY_SEPARATOR)
    //                    ->append($this->getFallbackLocale())
    //                ;
    //            }
    //
    //            // No docs present
    //            if (! File::exists($localizedPath)) {
    //                return [];
    //            }
    //
    //            $rows->push(...FlatfileParser::make($panelId, $localizedPath)->get());
    //            continue;
    ////            // Get all groups and parents
    ////            $groups = [];
    ////            $parents = [];
    ////            if ($panelId === 'kb-workspace') {
    ////                $directories = collect(File::allFiles($localizedPath))
    ////                    ->map(function ($file) {
    ////                        return dirname($file);
    ////                    })
    ////                    ->unique()
    ////                    ->values()
    ////                    ->all()
    ////                ;
    ////
    ////                foreach ($directories as $directory) {
    ////                    if (File::exists("$directory/_parent.md")) {
    ////                        $parents[] = [
    ////                            'id' => 'est'
    ////                        ];
    ////                    }
    ////                    if (File::exists("$directory/_group.md")) {
    ////                        $groups[] = [
    ////                            'id' => 'est'
    ////                        ];
    ////                    }
    ////                }
    ////            }
    //
    //            $rows->push(
    //                ...collect(File::allFiles($localizedPath))
    //                    ->map(function (\SplFileInfo $file) use ($panelId, $localizedPath) {
    //                        $data = KnowledgeBase::parseMarkdown($file->getRealPath());
    //                        if ($panelId === 'kb-workspace') {
    ////                        dd($file->getPathname(), $file->getRealPath(), dirname($file));
    //
    //                        $parentFilePath = dirname($file) . '.md';
    //                        if (File::exists($parentFilePath)) {
    //                            $parentFile = new \SplFileInfo($parentFilePath);
    ////                            dd($parentFile);
    //                        }
    //                        }
    //
    //                        $id = str($file->getRealPath())
    //                            ->afterLast($localizedPath)
    //                            ->beforeLast($file->getExtension())
    //                            ->replace(DIRECTORY_SEPARATOR, '.')
    //                            ->trim('.')
    //                        ;
    //
    //                        $parts = $id->explode('.');
    //                        $group = data_get($data, 'front-matter.group');
    //                        $parent = data_get($data, 'front-matter.parent');
    //
    //                        $depth = count($parts);
    //
    //                        if ($depth > 3) {
    //                            throw new \Exception("The documentation file \"$id\" is nested too deeply. The maximum nesting depth is 3.");
    //                        }
    //
    //                        if (count($parts) >= 2) {
    //                            $group ??= Str::headline($parts[0]);
    //                        }
    //                        if (count($parts) >= 3) {
    //                            $parent ??= Str::headline($parts[1]);
    //                        }
    //
    //                        return [
    //                            'id' => $id->toString(),
    //                            'slug' => $id->replace('.', '/')->toString(),
    //                            'path' => $file->getRealPath(),
    //                            'content' => data_get($data, 'html'),
    //                            'title' => data_get($data, 'front-matter.title', $id->afterLast('.')->headline()),
    //                            'group' => $group,
    //                            'icon' => data_get($data, 'front-matter.icon'),
    //                            'parent' => $parent,
    //                            'order' => data_get($data, 'front-matter.order'),
    //                            'panel_id' => $panelId,
    //                        ];
    //                    })
    //                    ->toArray()
    //            );
    //        }
    //
    //        return $rows->toArray();
    //    }
    //
    //    public function getFrontMatter(): array
    //    {
    //        $result = $this->getHtml();
    //
    //        if ($result instanceof RenderedContentWithFrontMatter) {
    //            return $result->getFrontMatter();
    //        }
    //
    //        return [];
    //    }
    //
    public function getHtml(): RenderedContentInterface
    {
        $converter = app(MarkdownRenderer::class);

        dump($this->path);

        return $converter->convert(file_get_contents($this->path));
    }

    public function getSimpleHtml(): RenderedContentInterface
    {
        $converter = app(MarkdownRenderer::class)->minimal();

        return $converter->convert(file_get_contents($this->path));
    }

    public function getUrl(): string
    {
        return ViewDocumentation::getUrl(parameters: [
            'record' => $this,
        ], panel: $this->panel_id);
    }

    //
    public function getAnchors()
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

    public function resolveRouteBindingQuery($query, $value, $field = null): Builder
    {
        return parent::resolveRouteBindingQuery($query, $value, $field)
            ->where('panel_id', KnowledgeBase::panel()->getId())
        ;
    }

    //
    public function getRouteKeyName()
    {
        return 'slug';
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
        return static::find($this->parent_id);
    }

    public function children(): \Illuminate\Database\Eloquent\Collection
    {
        return static::query()
            ->where('panel_id', $this->panel_id)
            ->where('parent_id', $this->id)
            ->get()
        ;
    }

    //
    //    public function getParent(): ?string
    //    {
    //        return $this->parent;
    //    }
    //
    //    public function getParentId(): ?string
    //    {
    //        $parts = str($this->getId())->explode('.', 3);
    //        if (count($parts) >= 3) {
    //            return $parts[0] . '.' . $parts[1];
    //        } elseif (count($parts) == 2) {
    //            return $parts[0];
    //        }
    //
    //        return null;
    //    }
    //
    //    public function getGroup(): ?string
    //    {
    //        return $this->group;
    //    }
    //
    //    public function getOrder(): int
    //    {
    //        return $this->order ?? 0;
    //    }
    //
    //    public function getIcon(): ?string
    //    {
    //        return $this->icon ?? 'heroicon-o-document';
    //    }
    //
    //    public function isRegistered(): bool
    //    {
    //        return ! empty($this->getTitle());
    //    }
    //
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

        //                ->when(
        //                    $group = $this->getGroup(),
        //                    fn (Collection $collection) => $collection->put($this->getGroupUrl() . '#', $group)
        //                )
        //                ->when(
        //                    $parent = $this->getParent(),
        //                    fn (Collection $collection) => $collection->put(
        //                        KnowledgeBase::documentable($this->getParentId())->getUrl(),
        //                        $parent,
        //                    )
        //                )
        //                ->put($this->getUrl(), $this->getTitle())
        //                ->toArray()
        //            ;
    }

    //
    //    public function getGroupUrl()
    //    {
    //        $group = collect(KnowledgeBase::panel()->getNavigation())
    //            ->first(function ($item) {
    //                return $item instanceof NavigationGroup && $item->getLabel() === $this->getGroup();
    //            })
    //        ;
    //
    //        if ($group) {
    //            return Arr::first($group->getItems())->getUrl();
    //        }
    //
    //        return null;
    //    }

    public function isRegistered(): bool
    {
        // TODO: Implement isRegistered() method.
        return true;
    }

    public function getParent(): ?string
    {
        // TODO: Implement getParent() method.
    }

    public function getGroup(): ?string
    {
        // TODO: Implement getGroup() method.
    }

    public function getOrder(): int
    {
        // TODO: Implement getOrder() method.
    }

    public function getIcon(): ?string
    {
        // TODO: Implement getIcon() method.
    }

    public function toNavigationItem(): NavigationItem
    {
        if ($this->type === NodeType::Group) {
            throw new \Exception('Cannot convert a group to a navigation item');
        }

        $item = NavigationItem::make($this->title)
            ->icon($this->icon)
            ->sort($this->order)
            ->url($this->getUrl())
            ->isActiveWhen(fn () => url()->current() === $this->getUrl())
        ;

        if ($parent = $this->parent()) {
            match ($parent->type) {
                NodeType::Group => $item->group($parent->title),
                default => $item
                    ->parentItem($parent->title)
                    ->group($parent->parent()?->title),
            };
        }

        return $item;
    }

    public function toNavigationGroup(): NavigationGroup
    {
        if ($this->type !== NodeType::Group) {
            throw new \Exception('Cannot convert a document to a navigation group');
        }

        return NavigationGroup::make($this->title)
            ->icon($this->icon)
        ;
        //            ->items(
        //                KnowledgeBase::model()::query()
        //                    ->where('parent_id', $this->id)
        //                    ->get()
        //                    ->filter(fn (Documentable $documentable) => $documentable->isRegistered())
        //                    ->sort(fn (Documentable $d1, Documentable $d2) => $d1->getOrder() <=> $d2->getOrder())
        //                    ->map(fn (Documentable $documentable) => $documentable->toNavigationItem())
        //                    ->toArray()
        //            );
    }
}
