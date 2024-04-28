<?php

namespace Guava\FilamentKnowledgeBase\Pages;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Panel;
use Guava\FilamentKnowledgeBase\Clusters\Test;
use Guava\FilamentKnowledgeBase\Markdown\MarkdownRenderer;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;

class DocumentationPage extends Page
{
    protected static string $view = 'filament-knowledge-base::pages.section';

    protected static ?string $navigationGroup = 'asd';

    public array $frontMatter = [];

    public array $anchors = [];

    public ?string $content = null;

    //    protected static ?string $cluster = Test::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public function mount($path)
    {
        $locale = App::getLocale();
        $file = base_path("docs/$locale/$path.md");

        if (! file_exists($file)) {
            abort(404);
        }

        $content = file_get_contents($file);

        $converter = app(MarkdownRenderer::class);
        $result = $converter->convertToHtml($content);

        $this->content = $result->getContent();
        $this->frontMatter = $result->getFrontMatter();
        //        dd($result->getDocument()->walker());
        $walker = $result->getDocument()->walker();
        $anchors = [];
        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($event->isEntering() && $node instanceof HeadingPermalink) {
                $slug = $node->getSlug();
                //                dd($node);
                //                dd($node->getStringContent());
                // We're entering a Heading node

                // Method 'getStringContent' will give us the text of the header
                // This allows us to form the slug (anchor) the same way GitHub, CommonMark and others do
                // (text lowercased, spaces are replaced with dashes, special characters removed)
                //                $slug = preg_replace('/\s+/', '-', strtolower($node->getStringContent()));

                // Add to the list of anchors
                $anchors[] = $slug;
            }
        }
        $this->anchors = $anchors;
    }

    public static function getRoutePath(): string
    {
        return '/docs/{path?}';
    }

    public static function routes(Panel $panel): void
    {
        Route::get(static::getRoutePath(), static::class)
            ->middleware(static::getRouteMiddleware($panel))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->name(static::getRelativeRouteName())
            ->where('path', '.*')
        ;
    }

    public function getSubNavigation(): array
    {
        $pages = [];
        foreach ($this->anchors as $anchor) {
            for ($i = 0; $i < 10; $i++) {
                $pages[] = NavigationItem::make($anchor)
                    ->url("#$anchor-$i")
//                ->icon('heroicon-o-chevron-right')
                ;
            }
        }

        return $pages;

        return [
            NavigationItem::make('test'), //'asd' => 'asd',
        ];
    }
}
