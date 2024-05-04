<?php

namespace Guava\FilamentKnowledgeBase\Models;

use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Guava\FilamentKnowledgeBase\Filament\Pages\ViewDocumentation;
use Guava\FilamentKnowledgeBase\Markdown\MarkdownRenderer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;
use League\CommonMark\Output\RenderedContentInterface;
use Spatie\StructureDiscoverer\Discover;
use Sushi\Sushi;

class FlatfileDocumentation extends Model implements Documentable
{
    use Sushi;

    public $incrementing = false;

    public function getRows()
    {
        $path = base_path(
            str(config('filament-knowledge-base.docs-path'))
                ->append('/')
                ->append(App::getLocale())
        );

        return collect(File::allFiles($path))
            ->map(function (\SplFileInfo $file) use ($path) {
                $data = KnowledgeBase::parseMarkdown($file->getRealPath());

                $id = str($file->getPathname())
                    ->afterLast($path)
                    ->beforeLast($file->getExtension())
                    ->replace('/', '.')
                    ->trim('.')
                ;

                $parts = $id->explode('.', 3);
                $group = data_get($data, 'front-matter.group');
                $parent = data_get($data, 'front-matter.parent');
                if (count($parts) >= 2) {
                    $group ??= Str::headline($parts[0]);
                }
                if (count($parts) >= 3) {
                    $parent ??= Str::headline($parts[1]);
                }

                return [
                    'id' => $id,
                    'slug' => $id->replace('.', '/')->toString(),
                    'path' => $file->getRealPath(),
                    'content' => data_get($data, 'html'),
                    'title' => data_get($data, 'front-matter.title', $id->afterLast('.')->headline()),
                    'group' => $group,
                    'icon' => data_get($data, 'front-matter.icon'),
                    'parent' => $parent,
                    'order' => data_get($data, 'front-matter.order'),
                ];
            })
            ->toArray()
        ;
        collect(Discover::in(app_path('KnowledgeBasePanel'))
            ->extending(\Guava\FilamentKnowledgeBase\Pages\Documentation::class)
            ->get());
    }

    public function getFrontMatter(): array
    {
        $result = $this->getHtml();

        if ($result instanceof RenderedContentWithFrontMatter) {
            return $result->getFrontMatter();
        }

        return [];
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

    public function getPart(string $id)
    {
        $walker = $this->getHtml()->getDocument()->walker();

        while ($event = $walker->next()) {
            $node = $event->getNode();
            if ($node instanceof Heading) {
            }
        }

    }

    public function getUrl(): string
    {
        return ViewDocumentation::getUrl(parameters: [
            'record' => $this,
        ], panel: config('filament-knowledge-base.panel.id'));
    }

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
                //                    dd($node, $node->next());
                //                }
                $anchors[$next->getLiteral()] = $slug;
            }
        }

        return $anchors;
    }

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

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getOrder(): int
    {
        return $this->order ?? 0;
    }

    public function getIcon(): ?string
    {
        return $this->icon ?? 'heroicon-o-document';
    }

    public function isRegistered(): bool
    {
        return ! empty($this->getTitle());
    }

    public function getBreadcrumbs(): array
    {
        return collect([
            __('filament-knowledge-base::translations.knowledge-base'),
        ])
            ->when(
                $group = $this->getGroup(),
                fn (Collection $collection) => $collection->push($group)
            )
            ->when(
                $parent = $this->getParent(),
                fn (Collection $collection) => $collection->push($parent)
            )
            ->put(ViewDocumentation::getUrl([
                'record' => $this,
            ], panel: 'knowledge-base'), $this->getTitle())
            ->toArray()
        ;
    }
}
