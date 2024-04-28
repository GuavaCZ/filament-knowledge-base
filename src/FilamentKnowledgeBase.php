<?php

namespace Guava\FilamentKnowledgeBase;

use Filament\Actions\Action;
use Guava\FilamentKnowledgeBase\Pages\Documentation;

class FilamentKnowledgeBase
{
    /**
     * @param  string|Documentation  $class
     * @return Action
     */
    public function getDocumentationAction(string $class)
    {
        $reflection = new \ReflectionClass($class);
        $property = $reflection->getProperty('title');
        $property->setAccessible(true);

        return Action::make(class_basename($class))
            ->label($property->getValue())
            ->icon($class::getNavigationIcon())
            ->url($class::getUrl(panel: config('filament-knowledge-base.id')))
        ;
    }
}
