<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use PhpParser\Comment\Doc;

trait HasDocumentable
{
    protected Documentable $documentable;

    public function documentable(Documentable $documentable): static
    {
        $this->documentable = $documentable;

        return $this;
    }

    public function getDocumentable(): Documentable
    {
        return $this->documentable;

    }
}
