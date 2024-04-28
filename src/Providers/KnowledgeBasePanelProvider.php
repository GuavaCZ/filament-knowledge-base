<?php

namespace Guava\FilamentKnowledgeBase\Providers;

use Closure;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Guava\FilamentKnowledgeBase\Markdown\MarkdownRenderer;
use Guava\FilamentKnowledgeBase\Pages\Chapter;
use Guava\FilamentKnowledgeBase\Pages\Documentation;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use Spatie\StructureDiscoverer\Discover;
use Symfony\Component\Finder\SplFileInfo;

class KnowledgeBasePanelProvider extends PanelProvider
{
    protected static ?Closure $configureUsing = null;

    public static function configureUsing(Closure $modifyUsing): void
    {
        static::$configureUsing = $modifyUsing;
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id(config('filament-knowledge-base.id'))
            ->path(config('filament-knowledge-base.path'))
            ->pages([
//                Dashboard::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->when($configure = static::$configureUsing, fn (Panel $panel) => $configure($panel))

            ->viteTheme(config('filament-knowledge-base.theme-path'))
//            ->pages($this->getPages())
            ->discoverPages(in: app_path('KnowledgeBase'), for: 'App\\KnowledgeBase')
//            ->discoverPages(in: app_path('KnowledgeBase'), for: 'App\\Filament\\Pages')
            ->discoverClusters(__DIR__ . '/../Clusters', 'Guava\\FilamentKnowledgeBase\\Clusters')
//            ->pages($this->getPages())
//            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
//            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
//            ->pages([Dashboard::class, ...Discover::in(app_path('Filament/KnowledgeBase'))
//                    ->classes()
//                    ->extending(Chapter::class)
//                    ->get()])
//            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->when(
                ! config('filament-knowledge-base.guest-access'),
                fn (Panel $panel) => $panel
                    ->widgets([
                        AccountWidget::class,
                    ])
                    ->authMiddleware([
                        Authenticate::class,
                    ])
            )
//            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
//                $chapters = Discover::in(app_path('Filament/KnowledgeBase'))
//                    ->classes()
//                    ->extending(Chapter::class)
//                    ->get();
//
//                foreach ($chapters as $chapter) {
//                    $chapter = new $chapter;
//                    $builder->item($chapter->getNavigationItem())
////                        ->icon($chapter::getIcon())
////                        ->url(fn (): string => $chapter::getUrl())
//                    ;
//                }
//                return $builder;
////                return $builder->item()
//                return $builder->items([
//                    NavigationItem::make('Dashboard2')
//                        ->icon('heroicon-o-home')
////                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
//                        ->url(fn (): string => Dashboard::getUrl()),
//                ]);
//            })
//            ->navigation($this->buildNavigation(...))
        ;
    }

    protected function getPages()
    {
        $pages = Discover::in(app_path('KnowledgeBase'))
            ->classes()
            ->extending(Documentation::class)
            ->get()
        ;

        return $pages;

        /** @var \SplFileInfo $info */
        $files = File::files(base_path('docs'));

        $pages = [];

        $converter = app(MarkdownRenderer::class);
        foreach ($files as $file) {
            $content = $file->getContents();
            $result = $converter->convertToHtml($content);

        }

        return;
        dd(docs_path());
        $converter = app(MarkdownRenderer::class);
        $result = $converter->convertToHtml($markdown);
        if ($result instanceof RenderedContentWithFrontMatter) {
            $frontMatter = $result->getFrontMatter();
            dd($result, $frontMatter);
        }

        dd($result);
    }

    protected function buildNavigation(NavigationBuilder $builder)
    {
        $locale = App::getLocale();
        $groups = File::directories(base_path("docs/{$locale}"));
        $items = File::files(base_path("docs/{$locale}"));

        $items = array_merge($groups, $items);

        $items = Arr::sort($items, function ($a, $b) {
            $nameA = $a instanceof SplFileInfo ? $a->getBasename() : str($a)->afterLast('/');
            $nameB = $b instanceof SplFileInfo ? $b->getBasename() : str($b)->afterLast('/');

            return ! strcasecmp($nameA, $nameB);
        });

        $index = 0;
        foreach ($items as $item) {
            if ($item instanceof SplFileInfo) {
                $builder->item(NavigationItem::make($item->getBasename())->sort($index));
            } else {
                $children = File::files($item);
                $builder->group(NavigationGroup::make(str($item)->afterLast('/')->toString())
                    ->items(
                        collect($children)
                            ->map(fn (SplFileInfo $item) => NavigationItem::make($item->getBasename()))
                            ->toArray()
                    ));
            }
            $index++;
        }

        return $builder;
    }
}
