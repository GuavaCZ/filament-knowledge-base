<?php

use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBaseCompanionPlugin;

it('keeps the open documentation button by default', function () {
    expect(KnowledgeBaseCompanionPlugin::make()->shouldDisableOpenDocumentationButton())->toBeFalse();
});

it('can disable the open documentation button', function () {
    expect(KnowledgeBaseCompanionPlugin::make()->disableOpenDocumentationButton()->shouldDisableOpenDocumentationButton())
        ->toBeTrue()
    ;
});

it('can re-enable the open documentation button', function () {
    expect(KnowledgeBaseCompanionPlugin::make()->disableOpenDocumentationButton(false)->shouldDisableOpenDocumentationButton())
        ->toBeFalse()
    ;
});

it('returns the plugin so the call can be chained', function () {
    $plugin = KnowledgeBaseCompanionPlugin::make();

    expect($plugin->disableOpenDocumentationButton())->toBe($plugin);
});
