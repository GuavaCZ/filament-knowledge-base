<?php

namespace Guava\FilamentKnowledgeBase\Plugins;

use Composer\InstalledVersions;
use Filament\Actions\Action;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationManager as BaseNavigationManager;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\View\PanelsRenderHook;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableBreadcrumbs;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableDefaultClasses;
use Guava\FilamentKnowledgeBase\Concerns\HasAnchorSymbol;
use Guava\FilamentKnowledgeBase\Concerns\HasArticleClass;
use Guava\FilamentKnowledgeBase\Concerns\HasBackToDefaultPanelButton;
use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Guava\FilamentKnowledgeBase\Documentation;
use Guava\FilamentKnowledgeBase\Enums\TableOfContentsPosition;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Guava\FilamentKnowledgeBase\Filament\Navigation\Navigation;
use Guava\FilamentKnowledgeBase\Filament\Resources\DocumentationResource;
use Guava\FilamentKnowledgeBase\KnowledgeBaseRegistry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Spatie\StructureDiscoverer\Discover;

class KnowledgeBasePlugin implements Plugin
{
    use CanDisableBreadcrumbs;
    use CanDisableDefaultClasses;
    use EvaluatesClosures;
    use HasAnchorSymbol;
    use HasArticleClass;
    use HasBackToDefaultPanelButton;

    public const ID = 'guava::filament-knowledge-base';

    protected static bool $syntaxHighlighting = false;

    protected bool $disableTableOfContents = false;

    protected TableOfContentsPosition $tableOfContentsPosition = TableOfContentsPosition::End;

    protected ?string $docsPath = null;

    public function __construct(?string $docsPath = null)
    {
        $this->docsPath = $docsPath;
    }

    public function syntaxHighlighting(bool $condition = true): static
    {
        static::$syntaxHighlighting = $condition;

        if (static::$syntaxHighlighting) {
            if (! InstalledVersions::isInstalled('spatie/shiki-php')) {
                throw new Exception('You need to install shiki and spatie/shiki-php in order to use the syntax highlighting feature. Please check the documentation for installation instructions.');
            }
        }

        return $this;
    }

    public static function hasSyntaxHighlighting(): bool
    {
        return static::$syntaxHighlighting;
    }

    public function disableTableOfContents(bool $condition = true): static
    {
        $this->disableTableOfContents = $condition;

        return $this;
    }

    public function shouldDisableTableOfContents(): bool
    {
        return $this->evaluate($this->disableTableOfContents);
    }

    public function tableOfContentsPosition(TableOfContentsPosition $position): static
    {
        $this->tableOfContentsPosition = $position;

        return $this;
    }

    public function getTableOfContentsPosition(): TableOfContentsPosition
    {
        return $this->evaluate($this->tableOfContentsPosition);
    }

    public function getDocsPath(): string
    {
        return $this->docsPath;
    }

    public function getId(): string
    {
        return static::ID;
    }

    public function register(Panel $panel): void
    {
        $this->docsPath ??= base_path("docs/{$panel->getId()}");

        $panel->resources([
            DocumentationResource::class,
        ]);

        $panel
            ->when(
                ! $this->shouldDisableBackToDefaultPanelButton(),
                fn (Panel $panel) => $panel
                    ->renderHook(
                        PanelsRenderHook::SIDEBAR_FOOTER,
                        fn (): string => $this->getBackToDefaultPanelButton()->render(),
                    )
            )
        ;

        //        $panel
        //            ->renderHook(
        //                $this->getHelpMenuRenderHook(),
        //                fn (): string => Blade::render('@livewire(\'help-menu\')'),
        //            )
        //            ->when(
        //                ! $this->shouldDisableModalLinks(),
        //                fn (Panel $panel) => $panel->renderHook(
        //                    PanelsRenderHook::BODY_END,
        //                    fn (): string => Blade::render('@livewire(\'modals\')'),
        //                )
        //            )
        //            ->when(
        //                ! $this->shouldDisableKnowledgeBasePanelButton(),
        //                fn (Panel $panel) => $panel
        //                    ->renderHook(
        //                        PanelsRenderHook::SIDEBAR_FOOTER,
        //                        fn (): string => view('filament-knowledge-base::sidebar-action', [
        //                            'label' => __('filament-knowledge-base::translations.knowledge-base'),
        //                            'icon' => 'heroicon-o-book-open',
        //                            'url' => \Guava\FilamentKnowledgeBase\Facades\KnowledgeBase::url(
        //                                \Guava\FilamentKnowledgeBase\Facades\KnowledgeBase::panel()
        //                            ),
        //                            'shouldOpenUrlInNewTab' => $this->shouldOpenDocumentationInNewTab(),
        //                        ])
        //                    )
        //            )
        //        ;
        app(KnowledgeBaseRegistry::class)->docsPath($panel->getId(), $this->getDocsPath());


    }

    public function boot(Panel $panel): void
    {
        Navigation::make($panel)->build();
//        $panel->navigation(fn (NavigationBuilder $builder) => $this->makeNavigation($panel, $builder));
    }

    public static function make(?string $docsPath = null): static
    {
        return app(static::class, [
            'docsPath' => $docsPath,
        ]);
    }

    protected function buildNavigationItem(Documentable $documentable)
    {
        return NavigationItem::make($documentable->getTitle())
            ->group($documentable->getGroup())
            ->icon($documentable->getIcon())
            ->sort($documentable->getOrder())
            ->childItems(
                KnowledgeBase::model()::query()
                    ->where('parent', $documentable->getTitle())
                    ->get()
                    ->filter(fn (Documentable $documentable) => $documentable->isRegistered())
                    ->sort(fn (Documentable $d1, Documentable $d2) => $d1->getOrder() <=> $d2->getOrder())
                    ->map(fn (Documentable $documentable) => $this->buildNavigationItem($documentable))
                    ->toArray()
            )
            ->parentItem($documentable->getParent())
            ->url($documentable->getUrl())
            ->isActiveWhen(fn () => url()->current() === $documentable->getUrl())
        ;
    }

    protected function makeNavigation(Panel $panel, NavigationBuilder $builder): NavigationBuilder
    {
        $documentables = KnowledgeBase::model()::query()
            ->where('panel_id', $panel->getId())
            ->get()
        ;

//        if (File::exists(app_path('Docs'))) {
//            $documentables
//                ->push(
//                    ...collect(Discover::in(app_path('Docs'))
//                        ->extending(Documentation::class)
//                        ->get())
//                        ->map(fn ($class) => new $class)
//                        ->all()
//                )
//            ;
//        }

        $documentables
            ->filter(fn (Documentable $documentable) => $documentable->isRegistered())
            ->filter(fn (Documentable $documentable) => $documentable->getParent() === null)
            ->groupBy(fn (Documentable $documentable) => $documentable->getGroup())
            ->map(
                fn (Collection $items, string $key) => empty($key)
                    ? $items
                        ->sort(fn (Documentable $d1, Documentable $d2) => $d1->getOrder() <=> $d2->getOrder())
                        ->map(fn (Documentable $documentation) => $this->buildNavigationItem($documentation))
                    : NavigationGroup::make($key)
                        ->items(
                            $items
                                ->sort(fn (Documentable $d1, Documentable $d2) => $d1->getOrder() <=> $d2->getOrder())
                                ->map(fn (Documentable $documentable) => $this->buildNavigationItem($documentable))
                                ->toArray()
                        )
            )
            ->flatten()
            ->each(fn ($item) => match (true) {
                $item instanceof NavigationItem => $builder->item($item),
                $item instanceof NavigationGroup => $builder->group($item),
            })
        ;

        return $builder;
    }
}
