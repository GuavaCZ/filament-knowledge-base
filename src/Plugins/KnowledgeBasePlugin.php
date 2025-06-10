<?php

namespace Guava\FilamentKnowledgeBase\Plugins;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\View\PanelsRenderHook;
use Guava\FilamentKnowledgeBase\Concerns\CanConfigureCommonMark;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableBreadcrumbs;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableFilamentStyles;
use Guava\FilamentKnowledgeBase\Concerns\CanDisableSyntaxHighlighting;
use Guava\FilamentKnowledgeBase\Concerns\HasAnchorSymbol;
use Guava\FilamentKnowledgeBase\Concerns\HasArticleClass;
use Guava\FilamentKnowledgeBase\Concerns\HasBackToDefaultPanelButton;
use Guava\FilamentKnowledgeBase\Concerns\HasTableOfContents;
use Guava\FilamentKnowledgeBase\Filament\Navigation\Navigation;
use Guava\FilamentKnowledgeBase\Filament\Resources\DocumentationResource;
use Guava\FilamentKnowledgeBase\KnowledgeBaseRegistry;

class KnowledgeBasePlugin implements Plugin
{
    use CanConfigureCommonMark;
    use CanDisableBreadcrumbs;
    use CanDisableFilamentStyles;
    use CanDisableSyntaxHighlighting;
    use EvaluatesClosures;
    use HasAnchorSymbol;
    use HasArticleClass;
    use HasBackToDefaultPanelButton;
    use HasTableOfContents;

    public const ID = 'guava::filament-knowledge-base';

    protected ?string $docsPath = null;

    public function __construct(?string $docsPath = null)
    {
        $this->docsPath = $docsPath;
    }

    public function getId(): string
    {
        return static::ID;
    }

    public function getDocsPath(): string
    {
        return $this->docsPath;
    }

    public function register(Panel $panel): void
    {
        $this->docsPath ??= base_path("docs/{$panel->getId()}");

        $panel->resources([
            DocumentationResource::class,
        ]);

        //            ->when(
        //                ! $this->shouldDisableBackToDefaultPanelButton(),
        //                fn (Panel $panel) => $panel
        //                    ->renderHook(
        //                        PanelsRenderHook::SIDEBAR_FOOTER,
        //                        fn (): string => $this->getBackToDefaultPanelButton()->render()->render(),
        //                    )
        //            )

        app(KnowledgeBaseRegistry::class)->docsPath($panel->getId(), $this->getDocsPath());

    }

    public function boot(Panel $panel): void
    {
        Navigation::make($panel)->build();
    }

    public static function make(?string $docsPath = null): static
    {
        return app(static::class, [
            'docsPath' => $docsPath,
        ]);
    }
}
