<?php

namespace Guava\FilamentKnowledgeBase\Pages;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Panel;
use Guava\FilamentKnowledgeBase\Clusters\Test;
use Guava\FilamentKnowledgeBase\Markdown\MarkdownRenderer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;

abstract class Documentation extends Page
{
    protected static string $view = 'filament-knowledge-base::pages.section';

    public static array $frontMatter = [];

    public ?string $html = null;

    public array $anchors = [];

    public static ?string $content = null;

    //    protected static ?string $cluster = Test::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public function mount()
    {
        $this->html = $this->getHtml();
    }

    public static function frontMatter(string $key, mixed $default = null)
    {
        return data_get(static::$frontMatter, $key, $default);
    }

    public static function content(): string
    {
        $locale = App::getLocale();
        $path = static::$content ?? str(static::getRelativeRouteName())
            ->after('docs.')
            ->replace('.', '/')
        ;
        $file = base_path("docs/$locale/$path.md");

        if (! file_exists($file)) {
            abort(404);
        }

        $content = file_get_contents($file);

        return $content;
    }

    protected function getHtml()
    {
        $converter = app(MarkdownRenderer::class);

        $result = $converter->convertToHtml($this->content());

        if ($result instanceof RenderedContentWithFrontMatter) {
            static::$frontMatter = $result->getFrontMatter();
        }

        return $result;
    }

    public static function getRelativeRouteName(): string
    {
        return str(static::class)
            ->after('App\\KnowledgeBase\\')
            ->explode('\\')
            ->map(fn (string $part) => str($part)->kebab())
            ->prepend('docs')
            ->join('.')
        ;

        //        return str('docs')
        //            ->when(
        //                $group = static::getNavigationGroup(),
        //                fn ($str) => $str->append('.')->append(str($group)->slug())
        //            )
        //            ->when(
        //                $parent = static::getNavigationParentItem(),
        //                fn ($str) => $str->append('.')->append(str($parent)->slug())
        //            )
        //            ->append('.')
        //            ->append(parent::getRelativeRouteName())
        //            ->toString()
        //        ;
    }

    public static function getRoutePath(): string
    {
        return str(static::getRelativeRouteName())->replace('.', '/');
        //        return str('docs')
        //            ->when(
        //                $group = static::getNavigationGroup(),
        //                fn ($str) => $str->append('/')->append(str($group)->slug())
        //            )
        //            ->when(
        //                $parent = static::getNavigationParentItem(),
        //                fn ($str) => $str->append('/')->append(str($parent)->slug())
        //            )
        //            ->append(parent::getRoutePath())
        //            ->toString()
        //        ;
    }

    //    public static function routes(Panel $panel): void
    //    {
    //        Route::get(static::getRoutePath(), static::class)
    //            ->middleware(static::getRouteMiddleware($panel))
    //            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
    //            ->name(static::getRelativeRouteName())
    //            ->where('path', '.*')
    //        ;
    //    }

    public function getAnchors()
    {
        $walker = $this->getHtml()->getDocument()->walker();
        $anchors = [];
        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($event->isEntering() && $node instanceof HeadingPermalink) {
                $slug = $node->getSlug();
                $anchors[$node->next()->getLiteral()] = $slug;
            }
        }

        return $anchors;
    }

    public function getBreadcrumbs(): array
    {
        return collect([
            //            'docs' => 'FlatfileDocumentation',
            'FlatfileDocumentation',
        ])
            ->when(
                $group = static::getNavigationGroup(),
                //                fn (Collection $collection) => $collection->put(str($group)->slug()->toString(), $group)
                fn (Collection $collection) => $collection->push($group)
            )
            ->when(
                $parent = static::getNavigationParentItem(),
                fn (Collection $collection) => $collection->push($parent)
            )
            ->push(static::getTitle())
            ->toArray()
        ;

        //        return collect([
        //            'docs' => 'FlatfileDocumentation',
        //        ])
        //            ->when(
        //                $group = static::getNavigationGroup(),
        //                fn (Collection $collection) => $collection->put(str($group)->slug(), $group)
        //            )
        //            ->when(
        //                $parent = static::getNavigationParentItem(),
        //                fn ($collection) => $collection->put(str($parent)->slug(), $parent)
        //            )
        //            ->toArray();
    }

    public function getSubNavigation(): array
    {
        $pages = [];
        foreach ($this->getAnchors() as $label => $anchor) {
            $pages[] = NavigationItem::make($anchor)
                ->url("#$anchor")
                ->label($label)
            ;
        }

        return $pages;
    }
}
