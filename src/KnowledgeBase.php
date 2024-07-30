<?php

namespace Guava\FilamentKnowledgeBase;

use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Panel;
use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Guava\FilamentKnowledgeBase\Filament\Panels\KnowledgeBasePanel;
use Guava\FilamentKnowledgeBase\Markdown\MarkdownRenderer;
use Guava\FilamentKnowledgeBase\Pages\Documentation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;

class KnowledgeBase
{
    /**
     * @param  string|Documentation  $class
     * @return Action
     */
    public function getDocumentationAction(Model $documentation)
    {
        return Action::make($documentation->slug)
            ->label($documentation->title)
            ->icon($documentation->icon)
            ->action(fn () => dd('test'))
            ->requiresConfirmation()
        ;
    }

    public function model(): Documentable | string
    {
        return config('filament-knowledge-base.model');
    }

    public function panel(): KnowledgeBasePanel
    {
        $panel = Filament::getPanel($this->panelId());

        if (! ($panel instanceof KnowledgeBasePanel)) {
            throw new Exception('Panel must be an Knowledge Base Panel!');
        }

        return $panel;
    }

    public function panelId(): string
    {
        return config('filament-knowledge-base.panel.id', 'knowledge-base');
    }

    public function url(Panel $panel): ?string
    {
        $oldPanel = Filament::getCurrentPanel();

        Filament::setCurrentPanel($panel);
        $url = $panel->getUrl();
        Filament::setCurrentPanel($oldPanel);

        return $url;
    }

    public function parseMarkdown(string $path): array
    {
        $converter = app(MarkdownRenderer::class);

        $result = $converter->convert(file_get_contents($path));

        $frontMatter = [];
        if ($result instanceof RenderedContentWithFrontMatter) {
            $frontMatter = $result->getFrontMatter();
        }

        return ['html' => $result->getContent(), 'front-matter' => $frontMatter];
    }

    public function documentable(Documentable | string $documentable): Documentable
    {
        if (is_string($documentable) && class_exists($documentable)) {
            $documentable = new $documentable;
        }

        if ($documentable instanceof Documentable) {
            return $documentable;
        }

        if (! is_string($documentable)) {
            throw new Exception('The class you provided is not a \`Documentable\`.');
        }

        if ($model = $this->model()::find(str($documentable)->replace('/', '.'))) {
            return $model;
        } else {
            throw new Exception("'The provided documentable \"$documentable\" could not be found.'");
        }
    }

    public function markdown(Documentable | string $documentable)
    {
        return new HtmlString($this->documentable($documentable)->getContent());
        if (is_string($documentable) && class_exists($documentable)) {
            $documentable = new $documentable;
        }

        if ($documentable instanceof Documentable) {
            return $documentable->getContent();
        }

        if (! is_string($documentable)) {
            throw new Exception('The class you provided is not a \`Documentable\`.');
        }

        $path = str(base_path(config('filament-knowledge-base.docs-path')))
            ->rtrim('/')
            ->append('/', App::getLocale(), '/')
        ;

        $documentable = str($documentable)->ltrim('/')
            ->replace('.', '/')
            ->prepend('/')
        ;
        $directory = $documentable->beforeLast('/')->ltrim('/')->append('/');
        $file = $documentable->afterLast('/')
            ->beforeLast('.md')
            ->append('.md')
        ;

        $fullPath = match (true) {
            File::exists($fullPath = str($path)->append($directory, '_partials/', $file)) => $fullPath,
            File::exists($fullPath = str($path)->append('_partials/', $directory, $file)) => $fullPath,
            File::exists($fullPath = str($path)->append($directory, $file)) => $fullPath,
        };

        $converter = app(MarkdownRenderer::class);

        return $converter->convertToHtml(file_get_contents($fullPath));
    }

    public function breadcrumbs(Documentable $documentable): HtmlString
    {
        return new HtmlString(view('filament::components.breadcrumbs', [
            'breadcrumbs' => $documentable->getBreadcrumbs(),
        ]));
    }
}
