<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;

/**
 * Blade only reports an unbalanced @if/@endif when the view is first rendered,
 * which nothing else in this suite does. Compiling every packaged view and
 * linting the result catches it at test time instead.
 */
it('compiles to valid php', function (string $view) {
    $compiled = Blade::compileString(File::get($view));

    $tmp = tempnam(sys_get_temp_dir(), 'kb-view-') . '.php';
    File::put($tmp, $compiled);

    exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($tmp) . ' 2>&1', $output, $status);
    File::delete($tmp);

    expect($status)->toBe(0, implode(PHP_EOL, $output));
})->with(function () {
    // Datasets are resolved before the application boots, so no facades here.
    $views = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(__DIR__ . '/../../resources/views', FilesystemIterator::SKIP_DOTS)
    );

    foreach ($views as $view) {
        if ($view->isFile() && str_ends_with($view->getFilename(), '.blade.php')) {
            yield $view->getRealPath();
        }
    }
});
