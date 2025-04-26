<?php

namespace Guava\FilamentKnowledgeBase;

use Exception;
use Filament\Actions\Action;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Guava\FilamentKnowledgeBase\Markdown\MarkdownRenderer;
use Guava\FilamentKnowledgeBase\Pages\Documentation;
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBaseCompanionPlugin;
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;
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

    /**
     * Attempts to get the knowledge base main plugin from the specified or current filament panel.
     *
     * @param  Panel|string|null  $panel  Panel to get the plugin from. If null, the current panel is used.
     *
     * @returns KnowledgeBasePlugin<Plugin>
     *
     * @throws Exception
     */
    public function plugin(Panel | string | null $panel = null): Plugin
    {
        $panel = match (true) {
            $panel instanceof Panel => $panel,
            is_string($panel) => Filament::getPanel($panel),
            default => Filament::getCurrentPanel(),
        };

        if (! $panel) {
            throw new Exception('Could not find the requested panel.');
        }

        if ($panel->hasPlugin(KnowledgeBasePlugin::ID)) {
            return $panel->getPlugin(KnowledgeBasePlugin::ID);
        }

        // Attempt to load the main plugin via the companion plugin
        if ($panel->hasPlugin(KnowledgeBaseCompanionPlugin::ID)) {
            return Filament::getPanel(
                $this->companion($panel)->getKnowledgeBasePanelId()
            )
                ->getPlugin(KnowledgeBasePlugin::ID)
            ;
        }

        throw new Exception('The requested panel does not have the knowledge base main plugin.');
    }

    /**
     * @return KnowledgeBaseCompanionPlugin<Plugin>
     *
     * @throws Exception
     */
    public function companion(Panel | string | null $panel = null): Plugin
    {
        $panel = match (true) {
            $panel instanceof Panel => $panel,
            is_string($panel) => Filament::getPanel($panel),
            default => Filament::getCurrentPanel(),
        };

        if (! $panel) {
            throw new Exception('Could not find the requested panel.');
        }

        if ($panel->hasPlugin(KnowledgeBaseCompanionPlugin::ID)) {
            return $panel->getPlugin(KnowledgeBaseCompanionPlugin::ID);
        }

        throw new Exception('The requested panel does not have the knowledge base companion plugin.');
    }

    public function panel(): Panel
    {
        $panel = Filament::getCurrentPanel();

        if ($panel->hasPlugin(KnowledgeBasePlugin::ID)) {
            return $panel;
        }

        if ($panel->hasPlugin(KnowledgeBaseCompanionPlugin::ID)) {
            $panel = Filament::getPanel(
                $panel->getPlugin(KnowledgeBaseCompanionPlugin::ID)->getKnowledgeBasePanelId()
            );
        }

        if (! $panel->hasPlugin(KnowledgeBasePlugin::ID)) {
            throw new Exception('Panel must be an Knowledge Base Panel!');
        }

        return $panel;
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
        //        dump($path);
        $result = $converter->convert(file_get_contents($path));
        //        dump($result);

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

        $panel = \Guava\FilamentKnowledgeBase\Facades\KnowledgeBase::panel();
//        $panelId = Facades\KnowledgeBase::companion()->getKnowledgeBasePanelId();
//        dd($panelId);
        if ($model = $this->model()::query()
            ->where('panel_id', $panel->getId())
            ->find(str($documentable)->replace('/', '.'))) {
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
