<?php

namespace Guava\FilamentKnowledgeBase\Filament\Resources;

use Filament\Panel;
use Filament\Resources\Resource;
use Guava\FilamentKnowledgeBase\Facades\KnowledgeBase;
use Guava\FilamentKnowledgeBase\Filament\Pages\ViewDocumentation;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DocumentationResource extends Resource
{
    public static function getModel(): string
    {
        return KnowledgeBase::model();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'title',
            'data',
        ];
    }

    //    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    //    {
    // //        $query->orWhereRaw("
    // //           json_extract(data, '$.content') LIKE '%$search%';
    // //        ");
    //        $query->orWhereLike('data', "%$search%");
    //    }

    protected static string | null | \BackedEnum $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPages(): array
    {
        return [
            'view' => ViewDocumentation::route('/{record?}'),
        ];
    }

    protected static bool $shouldRegisterNavigation = false;

    public static function getRoutePrefix(Panel $panel): string
    {
        return '';
    }

    public static function getGlobalSearchResultUrl(Model $record): ?string
    {
        return ViewDocumentation::getUrl(['record' => $record], panel: KnowledgeBase::panel()->getId());
    }

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->title;
        //        return str($record->slug)
        //            ->replace('/', ' -> ');
    }

    public static function resolveRecordRouteBinding(int | string $key, ?\Closure $modifyQuery = null): ?Model
    {
        // TODO: First try to load it from a standalone (App/Docs) class
        $record = parent::resolveRecordRouteBinding($key);

        if (! $record?->isActive()) {
            return null;
        }

        return $record;
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-knowledge-base::translations.knowledge-base');
    }
}
