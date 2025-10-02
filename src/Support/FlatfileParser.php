<?php

namespace Guava\FilamentKnowledgeBase\Support;

use Exception;
use Guava\FilamentKnowledgeBase\Enums\NodeType;
use Guava\FilamentKnowledgeBase\Markdown\MarkdownRenderer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use SplFileInfo;

class FlatfileParser
{
    protected string $renderer = MarkdownRenderer::class;

    protected array $results = [];

    public function __construct(
        protected string $panelId,
        protected string $path
    ) {}

    public function get(): Collection
    {
        collect(File::allFiles($this->path))
            ->each(fn (SplFileInfo $file) => $this->processFile($file))
        ;

        return collect(array_values($this->results));
    }

    /**
     * @throws Exception
     */
    protected function processFile(SplFileInfo $file): array
    {
        $data = (new $this->renderer)->convertAndReturnFluent(file_get_contents($file->getRealPath()));
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

        $result = [
            'id' => str($data->get('front-matter.id') ?? $id)
                ->prepend("$this->panelId.")
                ->toString(),
            'type' => $type,
            'slug' => $data->get('front-matter.slug') ?? str($id)->replace('.', '/')->toString(),
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
                default => $this->processDocumentationFile($file, $id, $data, $depth > 1),
            },
        ];

        $this->results[$id->toString()] = $result;

        return $result;
    }

    protected function processDir(SplFileInfo $dir): array
    {
        $id = str($dir->getRealPath())
            ->afterLast($this->path)
            ->replace(DIRECTORY_SEPARATOR, '.')
            ->trim('.')
        ;

        $parts = explode('.', $id);
        $depth = count($parts);

        if ($depth > 1) {
            throw new Exception("The documentation file \"$id\" can not be nested within more than one groups. You will need to create a parent documentation file at: [" . $dir->getRealPath() . '.md]');
        }

        $result = [
            'id' => str($id)
                ->prepend("$this->panelId.")
                ->toString(),
            'type' => NodeType::Group,
            'slug' => str($id)->replace('.', '/')->toString(),
            'path' => $dir->getRealPath(),
            'title' => Str::headline(File::name($dir->getRealPath())),
            'icon' => null,
            'order' => null,
            'active' => true,
            'parent_id' => null,
            'panel_id' => $this->panelId,
            ...$this->parseGroupFile($dir, $id, new Fluent([]), $depth > 1),
        ];

        $this->results[$id->toString()] = $result;

        return $result;
    }

    protected function parseGroupFile(SplFileInfo $file, string $id, Fluent $data, bool $checkParents = false): array
    {
        $result = [
            'data' => json_encode($this->getCustomData($data)),
        ];

        $parentDirPath = dirname($file->getRealPath());
        $parentFilePath = $parentDirPath . '.md';

        // We have an explicit parent group config
        if (File::exists($parentFilePath)) {
            $parentFile = $this->processFile(new SplFileInfo($parentFilePath));
            $result['parent_id'] = data_get($parentFile, 'id');
        } elseif ($checkParents) {
            $parentDir = $this->processDir(new SplFileInfo($parentDirPath));
            $result['parent_id'] = data_get($parentDir, 'id');
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    protected function processDocumentationFile(SplFileInfo $file, string $id, Fluent $data, bool $checkParents = false): array
    {
        $result = [
            'data' => json_encode([
                ...$this->getCustomData($data),
                'content' => $data->get('html'),
            ]),
        ];

        $parentDirPath = dirname($file->getRealPath());
        $parentFilePath = $parentDirPath . '.md';

        // We have an explicit parent config
        if (File::exists($parentFilePath)) {
            $parentFile = $this->processFile(new SplFileInfo($parentFilePath));
            $result['parent_id'] = data_get($parentFile, 'id');
        } elseif ($checkParents) {
            $parentDir = $this->processDir(new SplFileInfo($parentDirPath));
            $result['parent_id'] = data_get($parentDir, 'id');
        }

        return $result;
    }

    protected function processLinkFile(SplFileInfo $file, string $id, Fluent $data): array
    {
        return [
            'data' => json_encode([
                ...$this->getCustomData($data),
                'url' => $data->get('front-matter.url'),
            ]),
        ];
    }

    protected function getCustomData(Fluent $data): array
    {
        return $data
            ->collect('front-matter')
            ->except([
                'id',
                'type',
                'slug',
                'path',
                'title',
                'icon',
                'order',
                'active',
                'parent_id',
                'panel_id',
                'url',
                'data',
                'content',
            ])
            ->all()
        ;
    }

    public function renderer(string $renderer): static
    {
        $this->renderer = $renderer;

        return $this;
    }

    public static function make(string $panelId, string $path): static
    {
        return app(static::class, [
            'panelId' => $panelId,
            'path' => $path,
        ]);
    }
}
