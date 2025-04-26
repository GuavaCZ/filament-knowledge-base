<?php

namespace Guava\FilamentKnowledgeBase;

use Illuminate\Support\Collection;

class KnowledgeBaseRegistry
{
    protected array $docsPaths = [];

    public function docsPath(string $panel, string $path): static
    {
        data_set($this->docsPaths, $panel, $path);

        return $this;
    }

    public function getDocsPaths(): Collection
    {
        return collect($this->docsPaths);
    }
}
