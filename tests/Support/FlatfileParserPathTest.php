<?php

use Guava\FilamentKnowledgeBase\Support\FlatfileParser;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    fakeKnowledgeBasePlugin();
    config()->set('cache.default', 'array');

    $this->docs = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'kb-parser-' . getmypid();

    File::ensureDirectoryExists($this->docs . DIRECTORY_SEPARATOR . 'features');
    File::put($this->docs . DIRECTORY_SEPARATOR . 'getting-started.md', "# Getting started\n");
    File::put($this->docs . DIRECTORY_SEPARATOR . 'features' . DIRECTORY_SEPARATOR . 'search.md', "# Search\n");
});

afterEach(function () {
    File::deleteDirectory($this->docs);
});

it('derives the same ids however the docs path is spelled', function (callable $spell) {
    $rows = FlatfileParser::make('admin', $spell($this->docs))->get();

    // 'admin.features' is the group the nested directory generates.
    expect($rows->pluck('id')->sort()->values()->all())->toBe([
        'admin.features',
        'admin.features.search',
        'admin.getting-started',
    ]);
})->with([
    'as given' => [fn (string $path) => $path],
    'with a trailing separator' => [fn (string $path) => $path . DIRECTORY_SEPARATOR],
    // Not a substring of the realpath — the shape of the Windows failure in #104.
    'with a dot segment' => [fn (string $path) => $path . DIRECTORY_SEPARATOR . '.' . DIRECTORY_SEPARATOR],
    'with a redundant separator' => [fn (string $path) => $path . str_repeat(DIRECTORY_SEPARATOR, 2)],
]);

it('derives slugs independently of how the docs path is spelled', function () {
    $rows = FlatfileParser::make('admin', $this->docs . DIRECTORY_SEPARATOR . '.' . DIRECTORY_SEPARATOR)->get();

    expect($rows->pluck('slug')->sort()->values()->all())->toBe([
        'features',
        'features/search',
        'getting-started',
    ]);
});
