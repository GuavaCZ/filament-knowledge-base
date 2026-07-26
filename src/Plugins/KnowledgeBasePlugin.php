<?php

namespace Guava\FilamentKnowledgeBase\Plugins;

use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\View\PanelsRenderHook;
use Guava\FilamentKnowledgeBase\Concerns\CanConfigureCommonMark;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableBreadcrumbs;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableFilamentStyles;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableSyntaxHighlighting;
use Guava\FilamentKnowledgeBase\Concerns\HasAnchorSymbol;
use Guava\FilamentKnowledgeBase\Concerns\HasArticleClass;
use Guava\FilamentKnowledgeBase\Concerns\HasBackButton;
use Guava\FilamentKnowledgeBase\Concerns\HasTableOfContents;
use Guava\FilamentKnowledgeBase\Filament\Navigation\Navigation;
use Guava\FilamentKnowledgeBase\Filament\Resources\DocumentationResource;
use Guava\FilamentKnowledgeBase\KnowledgeBaseRegistry;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\ComponentAttributeBag;
use RuntimeException;

class KnowledgeBasePlugin implements Plugin
{
    use CanConfigureCommonMark;
    use CanDisableBreadcrumbs;
    use CanDisableFilamentStyles;
    use CanDisableSyntaxHighlighting;
    use EvaluatesClosures;
    use HasAnchorSymbol;
    use HasArticleClass;
    use HasBackButton;
    use HasTableOfContents;

    public const ID = 'guava::filament-knowledge-base';

    protected ?string $docsPath = null;

    public function __construct(?string $docsPath = null)
    {
        $this->docsPath = $docsPath === null
            ? null
            : static::normalizeDocsPath($docsPath);
    }

    public function getId(): string
    {
        return static::ID;
    }

    public function getDocsPath(): string
    {
        // Set in register(), which Filament always runs before the panel is used.
        return $this->docsPath ?? throw new RuntimeException(
            'The knowledge base docs path is only available once the plugin has been registered on a panel.'
        );
    }

    /**
     * Normalizes separators and resolves a relative path against the app root.
     */
    protected static function normalizeDocsPath(string $path): string
    {
        $path = rtrim(
            str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($path)),
            DIRECTORY_SEPARATOR,
        );

        return match (true) {
            str_starts_with($path, DIRECTORY_SEPARATOR) => $path,
            // Windows drive letters and UNC paths are already absolute.
            (bool) preg_match('/^[A-Za-z]:/', $path) => $path,
            default => base_path($path),
        };
    }

    public function register(Panel $panel): void
    {
        $this->docsPath ??= base_path('docs' . DIRECTORY_SEPARATOR . $panel->getId());

        $panel->resources([
            DocumentationResource::class,
        ]);

        $panel->when(
            ! $this->shouldDisableBackButton(),
            fn (Panel $panel) => $panel->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): string => Blade::render('filament-panels::components.sidebar.group', [
                    'attributes' => new ComponentAttributeBag([
                        'class' => 'px-4 pb-4 [&_.fi-sidebar-item]:rounded-lg [&_.fi-sidebar-item]:ring-1 [&_.fi-sidebar-item]:ring-gray-950/10 dark:[&_.fi-sidebar-item]:ring-white/20',
                    ]),
                    'label' => null,
                    'items' => [
                        $this->getBackButton(),
                    ],
                ])
            )
        );

        app(KnowledgeBaseRegistry::class)->docsPath($panel->getId(), $this->getDocsPath());

    }

    public function boot(Panel $panel): void
    {
        // Defer building navigation until Filament is serving a request to avoid early model autoloading
        Filament::serving(function () use ($panel) {
            Navigation::make($panel)->build();
        });
    }

    public static function make(?string $docsPath = null): static
    {
        return app(static::class, [
            'docsPath' => $docsPath,
        ]);
    }
}
