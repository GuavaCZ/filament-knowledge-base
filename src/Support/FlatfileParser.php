<?php

namespace Guava\FilamentKnowledgeBase\Support;

use Guava\FilamentKnowledgeBase\Enums\NodeType;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use SplFileInfo;

class FlatfileParser
{
    public function __construct(
        protected string $panelId,
        protected string $path
    ) {
    }

    public function get(): Collection
    {
        return collect(File::allFiles($this->path))
            ->map(function (SplFileInfo $file) {
                return $this->processFile($file);
            })
        ;
    }

    protected function processFile(SplFileInfo $file): array
    {
        $data = KnowledgeBase::parseMarkdown($file->getRealPath());
        $content = data_get($data, 'html');
        $frontMatter = fluent(data_get($data, 'front-matter'));
        $type = NodeType::tryFrom($frontMatter->get('type') ?? NodeType::Documentation->value);

        $id = str($file->getRealPath())
            ->afterLast($this->path)
            ->beforeLast($file->getExtension())
            ->replace(DIRECTORY_SEPARATOR, '.')
            ->trim('.')
        ;

        $parts = explode('.', $id);
        $depth = count($parts);

        if ($depth > 3) {
            throw new \Exception("The documentation file \"$id\" is nested too deeply. The maximum nesting depth is 3.");
        }

        return [
            'id' => "$this->panelId.$id",
            'type' => $type,
            'slug' => str($id)->replace('.', '/')->toString(),
            'path' => $file->getRealPath(),
            'title' => $frontMatter->get('title') ?? Str::headline(File::name($file->getRealPath())),
            'icon' => $frontMatter->get('icon'),
            'parent_id' => null,
            'order' => $frontMatter->get('order'),
            'panel_id' => $this->panelId,
            ...match ($type) {
                NodeType::Group => $this->parseGroupFile($file, $id, $content, $frontMatter),
                NodeType::Link => $this->processLinkFile($file, $id, $content, $frontMatter),
                default => $this->processDocumentationFile($file, $id, $content, $frontMatter),
            },
        ];
    }

    protected function parseGroupFile(SplFileInfo $file, string $id, string $content, Fluent $frontMatter): array
    {
        return [
            'data' => json_encode([]),
        ];
    }

    protected function processDocumentationFile(SplFileInfo $file, string $id, string $content, Fluent $frontMatter): array
    {
        $result = [
            'data' => json_encode([
                'content' => $content,
            ]),
        ];

        $parentFilePath = dirname($file->getRealPath()) . '.md';

        if (File::exists($parentFilePath)) {
            $parentFile = $this->processFile(new SplFileInfo($parentFilePath));
            $result['parent_id'] = data_get($parentFile, 'id');
        }

        return $result;
    }

    protected function processLinkFile(SplFileInfo $file, string $id, string $content, Fluent $frontMatter): array
    {
        return [
            'data' => json_encode([
                'url' => $frontMatter->get('url'),
            ]),
        ];
    }

    public static function make(string $panelId, string $path): static
    {
        return app(static::class, [
            'panelId' => $panelId,
            'path' => $path,
        ]);
    }
}
