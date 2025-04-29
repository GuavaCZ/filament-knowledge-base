<?php

namespace Guava\FilamentKnowledgeBase\Support;

use Exception;
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
    ) {}

    public function get(): Collection
    {
        return collect(File::allFiles($this->path))
            ->map(function (SplFileInfo $file) {
                return $this->processFile($file);
            })
        ;
    }

    /**
     * @throws Exception
     */
    protected function processFile(SplFileInfo $file): array
    {
        $data = KnowledgeBase::parseMarkdown($file->getRealPath());

        $type = NodeType::tryFrom($data->get('front-matter.type') ?? NodeType::Documentation->value);

        $id = str($file->getRealPath())
            ->afterLast($this->path)
            ->beforeLast($file->getExtension())
            ->replace(DIRECTORY_SEPARATOR, '.')
            ->trim('.')
        ;

        $parts = explode('.', $id);
        $depth = count($parts);

        if ($depth > 3) {
            throw new Exception("The documentation file \"$id\" is nested too deeply. The maximum nesting depth is 3.");
        }

        return [
            'id' => "$this->panelId.$id",
            'type' => $type,
            'slug' => str($id)->replace('.', '/')->toString(),
            'path' => $file->getRealPath(),
            'title' => $data->get('front-matter.title') ?? Str::headline(File::name($file->getRealPath())),
            'icon' => $data->get('front-matter.icon'),
            'order' => $data->get('front-matter.order'),
            'active' => $data->get('front-matter.active') ?? true,
            'parent_id' => null,
            'panel_id' => $this->panelId,
            ...match ($type) {
                NodeType::Group => $this->parseGroupFile($file, $id, $data),
                NodeType::Link => $this->processLinkFile($file, $id, $data),
                default => $this->processDocumentationFile($file, $id, $data),
            },
        ];
    }

    protected function parseGroupFile(SplFileInfo $file, string $id, Fluent $data): array
    {
        return [
            'data' => json_encode([]),
        ];
    }

    /**
     * @throws Exception
     */
    protected function processDocumentationFile(SplFileInfo $file, string $id, Fluent $data): array
    {
        $result = [
            'data' => json_encode([
                'content' => $data->get('html'),
            ]),
        ];

        $parentFilePath = dirname($file->getRealPath()) . '.md';

        if (File::exists($parentFilePath)) {
            $parentFile = $this->processFile(new SplFileInfo($parentFilePath));
            $result['parent_id'] = data_get($parentFile, 'id');
        }

        return $result;
    }

    protected function processLinkFile(SplFileInfo $file, string $id, Fluent $data): array
    {
        return [
            'data' => json_encode([
                'url' => $data->get('front-matter.url'),
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
