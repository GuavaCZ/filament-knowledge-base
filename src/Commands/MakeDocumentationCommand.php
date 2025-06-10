<?php

namespace Guava\FilamentKnowledgeBase\Commands;

use Guava\FilamentKnowledgeBase\KnowledgeBaseRegistry;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Stringable;

class MakeDocumentationCommand extends GeneratorCommand
{
    protected $signature = 'docs:make {panel} {name} {--L|locale=*}';

    protected $aliases = [
        'kb:make',
    ];

    protected $description = 'Creates a documentation page';

    protected $type = 'Documentation';

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/markdown.md.stub';
    }

    protected function qualifyClass($name): string
    {
        return $name;
    }

    //    protected function getPath($name)
    //    {
    //        dd(\app(KnowledgeBaseRegistry::class)->getDocsPaths());
    //        return str(base_path(config('filament-knowledge-base.docs-path')))
    //                ->rtrim('/')
    //                ->append(
    //                    '/',
    //                    str($name)
    //                        ->replaceEnd('.md', '')
    //                        ->append('.md')
    //                )
    //        ;
    //    }

    //    protected function getNameInput()
    //    {
    //        return str(parent::getNameInput())
    //            ->trim('/')
    //            ->replace('.', '/')
    //            ->when(
    //                ! $this->option('class'),
    //                fn (Stringable $str) => $str->prepend($this->currentLocale, '/')
    //            )
    //            ->toString()
    //        ;
    //    }

    protected string $currentLocale;

    public function handle()
    {
        dd(\app(KnowledgeBaseRegistry::class)->getDocsPaths());
        $path = str(base_path(config('filament-knowledge-base.docs-path')))
            ->rtrim('/')
            ->append('/')
            ->toString()
        ;
        if (! File::exists($path)) {
            File::makeDirectory($path);
        }
        $locales = $this->option('locale');
        $locales = empty($locales)
            ? File::directories($path)
            : $locales;

        if (empty($locales)) {
            $locales[] = App::getLocale();
        }

        foreach ($locales as $locale) {
            $this->currentLocale = str($locale)->afterLast('/')->toString();
            if (parent::handle() === false) {
                return false;
            }
        }

        return true;
    }
}
