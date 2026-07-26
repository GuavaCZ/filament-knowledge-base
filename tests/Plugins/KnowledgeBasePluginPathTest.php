<?php

use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;

it('normalizes the configured docs path', function (string $given) {
    expect(KnowledgeBasePlugin::make($given)->getDocsPath())
        ->toBe(base_path('docs' . DIRECTORY_SEPARATOR . 'admin'))
    ;
})->with([
    'unix separators' => 'docs/admin',
    'windows separators' => 'docs\admin',
    'mixed separators' => 'docs\admin/',
    'trailing separator' => 'docs/admin/',
    'surrounding whitespace' => ' docs/admin ',
]);

it('leaves an absolute docs path alone', function () {
    $path = DIRECTORY_SEPARATOR . 'srv' . DIRECTORY_SEPARATOR . 'docs';

    expect(KnowledgeBasePlugin::make($path)->getDocsPath())->toBe($path);
});

it('fails loudly when the docs path is read before the plugin is registered', function () {
    KnowledgeBasePlugin::make()->getDocsPath();
})->throws(RuntimeException::class);
