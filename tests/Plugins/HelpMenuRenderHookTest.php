<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Guava\FilamentKnowledgeBase\Contracts\HasKnowledgeBase;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Guava\FilamentKnowledgeBase\Models\FlatfileNode;
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBaseCompanionPlugin;
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Blade;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Output\RenderedContent;

function renderHelpMenuAtHook(string $hook, array | string $documentation): string
{
    $companion = KnowledgeBaseCompanionPlugin::make()
        ->knowledgeBasePanelId('knowledge-base')
        ->helpMenuRenderHook($hook)
        ->modalPreviews()
    ;

    $panel = Panel::make()->id('admin')->spa()->plugin($companion);
    Filament::setCurrentPanel($panel);
    $panel->boot();

    KnowledgeBase::shouldReceive('plugin')->andReturn(KnowledgeBasePlugin::make());
    KnowledgeBase::shouldReceive('companion')->andReturn($companion);
    KnowledgeBase::shouldReceive('documentable')->andReturnUsing(function ($id) {
        if ($id instanceof FlatfileNode) {
            return $id;
        }

        $document = Mockery::mock(FlatfileNode::class);
        $document->shouldReceive('getId')->andReturn($id);
        $document->shouldReceive('getTitle')->andReturn("Article {$id}");
        $document->shouldReceive('getIcon')->andReturn(null);
        $document->shouldReceive('getUrl')->andReturn("/docs/{$id}");
        $document->shouldReceive('getSimpleHtml')->andReturn(new RenderedContent(new Document, "<p>Content {$id}</p>"));

        return $document;
    });

    $controller = new class implements HasKnowledgeBase
    {
        public static array | string $documentation;

        public static function getDocumentation(): array | string
        {
            return self::$documentation;
        }
    };
    $controller::$documentation = $documentation;
    $route = new Route('GET', '/', fn () => null);
    $route->controller = $controller;
    request()->setRouteResolver(fn () => $route);

    return Blade::render(<<<'BLADE'
        <div x-persist="topbar">
            {{ \Filament\Support\Facades\FilamentView::renderHook($hook) }}
        </div>
        <section id="scripts">@stack('scripts')</section>
        BLADE, ['hook' => $hook]);
}

it('keeps the help menu lifecycle outside persisted hooks', function (string $hook, array | string $documentation) {
    $html = renderHelpMenuAtHook($hook, $documentation);
    [$topbar, $scripts] = explode('<section id="scripts">', $html, 2);

    expect($topbar)->toContain('id="gu-kb-help-menu-anchor" hidden')
        ->not->toContain('wire:id', 'x-teleport', 'Content ')
        ->and($scripts)->toContain('x-teleport.prepend="#gu-kb-help-menu-anchor"', 'wire:id')
    ;

    foreach ((array) $documentation as $id) {
        expect($scripts)->toContain("Content {$id}", "Article {$id}");
    }

    if ($documentation === []) {
        expect($scripts)->not->toContain('Content ', 'Article ');
    }
})->with([
    PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
    PanelsRenderHook::GLOBAL_SEARCH_AFTER,
    PanelsRenderHook::USER_MENU_BEFORE,
    PanelsRenderHook::USER_MENU_AFTER,
])->with([
    'no documentation' => [[]],
    'single article' => ['first'],
    'multiple articles' => [['first', 'second']],
]);

it('keeps the default help menu inline', function () {
    $html = renderHelpMenuAtHook(PanelsRenderHook::TOPBAR_END, 'first');
    [$topbar, $scripts] = explode('<section id="scripts">', $html, 2);

    expect($topbar)->toContain('wire:id')
        ->not->toContain('gu-kb-help-menu-anchor', 'x-teleport.prepend')
        ->and($scripts)->toContain('Content first')
    ;
});
