<?php

namespace Guava\FilamentKnowledgeBase;

use Exception;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Guava\FilamentKnowledgeBase\Contracts\Documentable;
use Guava\FilamentKnowledgeBase\Models\FlatfileNode;
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBaseCompanionPlugin;
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;
use Illuminate\Support\HtmlString;

class KnowledgeBase
{
    /**
     * @return class-string<FlatfileNode>
     */
    public function model(): string
    {
        return config('filament-knowledge-base.flatfile-model');
    }

    /**
     * Attempts to get the knowledge base main plugin from the specified or current filament panel.
     *
     * @param  Panel|string|null  $panel  Panel to get the plugin from. If null, the current panel is used.
     *
     * @throws Exception
     */
    public function plugin(Panel | string | null $panel = null): KnowledgeBasePlugin
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
            $plugin = $panel->getPlugin(KnowledgeBasePlugin::ID);

            if ($plugin instanceof KnowledgeBasePlugin) {
                return $plugin;
            }
        }

        // Attempt to load the main plugin via the companion plugin
        if ($panel->hasPlugin(KnowledgeBaseCompanionPlugin::ID)) {
            $plugin = Filament::getPanel(
                $this->companion($panel)->getKnowledgeBasePanelId()
            )->getPlugin(KnowledgeBasePlugin::ID);

            if ($plugin instanceof KnowledgeBasePlugin) {
                return $plugin;
            }
        }

        throw new Exception('The requested panel does not have the knowledge base main plugin.');
    }

    /**
     * @throws Exception
     */
    public function companion(Panel | string | null $panel = null): KnowledgeBaseCompanionPlugin
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
            $plugin = $panel->getPlugin(KnowledgeBaseCompanionPlugin::ID);

            if ($plugin instanceof KnowledgeBaseCompanionPlugin) {
                return $plugin;
            }
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
                $this->companion($panel)->getKnowledgeBasePanelId()
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

    public function documentable(Documentable | string $documentable, ?string $panelId = null): Documentable
    {
        if ($documentable instanceof Documentable) {
            return $documentable;
        }

        $panelId ??= static::panel()->getId();

        // getPanel() throws for unknown ids, so it can't be used as an existence check.
        if (! array_key_exists($panelId, Filament::getPanels())) {
            throw new Exception('The provided panel does not exist.');
        }

        if ($model = $this->model()::query()->find($panelId . '.' . $documentable)) {
            return $model;
        }

        throw new Exception("'The provided documentable \"$documentable\" could not be found.'");
    }

    public function markdown(Documentable | string $documentable): HtmlString
    {
        return new HtmlString($this->documentable($documentable)->getContent());
    }

    public function breadcrumbs(Documentable $documentable): HtmlString
    {
        return new HtmlString(view('filament::components.breadcrumbs', [
            'breadcrumbs' => $documentable->getBreadcrumbs(),
        ]));
    }
}
