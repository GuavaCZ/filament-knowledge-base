<?php

namespace Guava\FilamentKnowledgeBase\Commands;

use Filament\Facades\Filament;
use Filament\Panel;
use Guava\FilamentKnowledgeBase\Enums\NodeType;
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBasePlugin;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Stringable;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeDocumentationCommand extends GeneratorCommand
{
    protected $signature = 'docs:make {panel?} {type?} {name?} {--L|locale=*}';

    protected $aliases = [
        'kb:make',
    ];

    protected $description = 'Creates a documentation page';

    protected $type = 'Documentation';

    protected NodeType $nodeType = NodeType::Documentation;

    protected ?string $panelId = null;

    protected ?string $docsId = null;

    protected function getStub(): string
    {
        return match ($this->nodeType) {
            NodeType::Documentation => __DIR__ . '/../../stubs/documentation.md.stub',
            NodeType::Group => __DIR__ . '/../../stubs/group.md.stub',
            NodeType::Link => __DIR__ . '/../../stubs/link.md.stub',
        };
    }

    protected function qualifyClass($name): string
    {
        return $name;
    }

    protected function getPath($name): string
    {
        return str($this->getDocsPath())
            ->rtrim('/')
            ->when(
                $this->currentLocale,
                fn (Stringable $str) => $str
                    ->append(
                        '/',
                        $this->currentLocale
                    )
            )
            ->append(
                '/',
                str($name)
                    ->replaceEnd('.md', '')
                    ->append('.md')
            )
        ;
    }

    protected function getDocsPath(): string
    {
        /** @var KnowledgeBasePlugin $plugin */
        $plugin = Filament::getPanel($this->panelId)->getPlugin(KnowledgeBasePlugin::ID);

        return $plugin->getDocsPath();
    }

    protected function getNameInput(): string
    {
        return str($this->docsId)
            ->trim('/')
            ->replace('.', '/')
            ->toString()
        ;
    }

    protected ?string $currentLocale = null;

    public function handle(): bool
    {
        $knowledgeBasePanels = collect(Filament::getPanels())
            ->filter(static fn (Panel $panel) => $panel->hasPlugin(KnowledgeBasePlugin::ID))
            ->keys()
        ;

        if ($knowledgeBasePanels->isEmpty()) {
            error('Please create a knowledge base panel first.');

            return false;
        }

        $this->panelId = $knowledgeBasePanels->count() > 1
            ? select(
                label: 'Which knowledge base panel do you want to add a documentation page to?',
                options: $knowledgeBasePanels,
                required: true
            )
            : $knowledgeBasePanels->first();

        $this->nodeType = NodeType::from(
            select(
                label: 'What node type do you want to create?',
                options: collect(NodeType::cases())
                    ->mapWithKeys(fn (NodeType $type) => [$type->value => $type->name])->toArray(),
                default: NodeType::Documentation->value,
            )
        );

        $this->docsId = text(
            label: 'Enter the ID of the documentation page in dot-notation:',
            placeholder: 'Such as "users.introduction"',
            required: true,
            validate: ['name' => 'required|regex:/^[\w\-\.]+$/i']
        );

        $maximumNestingLevel = $this->nodeType === NodeType::Group ? 1 : 3;

        if (count(explode('.', $this->docsId)) > $maximumNestingLevel) {
            error("Maximum nesting level for type [{$this->nodeType->name}] is {$maximumNestingLevel}.");

            return false;
        }

        $docsPath = $this->getDocsPath();
        // Create docs path if not existing
        if (! File::exists($docsPath)) {
            File::makeDirectory(
                path: $docsPath,
                recursive: true
            );
        }
        $locales = $this->option('locale');
        $locales = empty($locales)
            ? File::directories($docsPath)
            : $locales;

        if (empty($locales)) {
            $locales = Arr::wrap(App::getLocale());
        }

        foreach ($locales as $locale) {
            $this->currentLocale = str($locale)->afterLast('/')->toString();
            if (parent::handle() === false) {
                return false;
            }
        }

        if ($this->nodeType === NodeType::Group) {
            info('A group needs one or more child items in order to appear in your knowledge base panel. Call this command again to create the items.');
        }

        return true;
    }
}
