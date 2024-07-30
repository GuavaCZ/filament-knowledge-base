<?php

namespace Guava\FilamentKnowledgeBase\Filament\Panels;

use Composer\InstalledVersions;
use Exception;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Support\Assets\Theme;
use Filament\Support\Enums\Platform;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableBackToDefaultPanelButton;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableBreadcrumbs;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableDefaultClasses;
use Guava\FilamentKnowledgeBase\Concerns\HasAnchorSymbol;
use Guava\FilamentKnowledgeBase\Concerns\HasArticleClass;
use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Guava\FilamentKnowledgeBase\Documentation;
use Guava\FilamentKnowledgeBase\Enums\TableOfContentsPosition;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Guava\FilamentKnowledgeBase\Filament\Resources\DocumentationResource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Spatie\StructureDiscoverer\Discover;

class KnowledgeBasePanel extends Panel
{
    use CanDisableBackToDefaultPanelButton;
    use CanDisableBreadcrumbs;
    use CanDisableDefaultClasses;
    use HasAnchorSymbol;
    use HasArticleClass;

    protected bool $guestAccess = false;

    protected static bool $syntaxHighlighting = false;

    protected bool $disableTableOfContents = false;

    protected TableOfContentsPosition $tableOfContentsPosition = TableOfContentsPosition::End;

    public function __construct()
    {
        $this->id(
            config('filament-knowledge-base.panel.id', 'knowledge-base')
        );
    }

    public function guestAccess(bool $condition = true): static
    {
        $this->guestAccess = $condition;

        return $this;
    }

    public function hasGuestAccess(): bool
    {
        return $this->evaluate($this->guestAccess);
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

    public function getBrandName(): string | Htmlable
    {
        return $this->evaluate($this->brandName)
            ?? __('filament-knowledge-base::translations.knowledge-base')
            ?? config('app.name');
    }

    public function getTheme(): Theme
    {
        if (! isset($this->viteTheme)) {
            throw new Exception('The knowledge base panel needs to be registered with a custom vite theme.');
        }

        return parent::getTheme();
    }

    public function getPath(): string
    {
        if (! empty($path = parent::getPath())) {
            return $path;
        }

        return config('filament-knowledge-base.panel.path', 'kb');
    }

    public function getPages(): array
    {
        return array_unique([
            ...parent::getPages(),
            Dashboard::class,
        ]);
    }

    public function getResources(): array
    {
        return array_unique([
            ...parent::getResources(),
            DocumentationResource::class,
        ]);
    }

    public function getMiddleware(): array
    {
        return [
            ...parent::getMiddleware(),

            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ];
    }

    public function getGlobalSearchKeyBindings(): array
    {
        if (! empty($keyBindings = parent::getGlobalSearchKeyBindings())) {
            return $keyBindings;
        }

        return ['mod+k'];
    }

    public function getGlobalSearchFieldSuffix(): ?string
    {
        if ($suffix = parent::getGlobalSearchFieldSuffix()) {
            return $suffix;
        }

        return match (Platform::detect()) {
            Platform::Windows, Platform::Linux => 'CTRL+K',
            Platform::Mac => '⌘K',
            Platform::Other => null,
        };
    }

    protected function setUp(): void
    {
        $this
            ->when(
                ! $this->hasGuestAccess(),
                fn (Panel $panel) => $panel
                    ->widgets([
                        AccountWidget::class,
                    ])
                    ->authMiddleware([
                        Authenticate::class,
                    ])
            )

            ->when(
                ! $this->shouldDisableBackToDefaultPanelButton(),
                fn (Panel $panel) => $panel
                    ->renderHook(
                        PanelsRenderHook::SIDEBAR_FOOTER,
                        fn (): string => view('filament-knowledge-base::sidebar-action', [
                            'label' => __('filament-knowledge-base::translations.back-to-default-panel'),
                            'icon' => 'heroicon-o-arrow-uturn-left',
                            'url' => KnowledgeBase::url(Filament::getDefaultPanel()),
                            'shouldOpenUrlInNewTab' => false,
                        ])
                    )
            )

            // TODO: Replace with ->navigationItems and ->navigationGroups to support custom pages
            ->navigation($this->makeNavigation(...))
        ;
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

    protected function makeNavigation(NavigationBuilder $builder): NavigationBuilder
    {
        $documentables = KnowledgeBase::model()::all();

        if (File::exists(app_path('Docs'))) {
            $documentables
                ->push(
                    ...collect(Discover::in(app_path('Docs'))
                        ->extending(Documentation::class)
                        ->get())
                        ->map(fn ($class) => new $class)
                        ->all()
                )
            ;
        }

        $documentables
            ->filter(fn (Documentable $documentable) => $documentable->isRegistered())
            ->filter(fn (Documentable $documentable) => $documentable->getParent() === null)
            ->groupBy(fn (Documentable $documentable) => $documentable->getGroup())
            ->map(
                fn (Collection $items, string $key) => empty($key)
                    ? $items->map(fn (Documentable $documentation) => $this->buildNavigationItem($documentation))
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
