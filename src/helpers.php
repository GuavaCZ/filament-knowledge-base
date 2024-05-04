<?php

use function Illuminate\Filesystem\join_paths;

if (! function_exists('docs_path')) {
    /**
     * Get the path to the docs folder.
     */
    function docs_path(string $path = '', ?string $locale = null): string
    {
        $locale ??= App::getLocale();

        return join_paths(base_path('docs'), $locale, $path);
    }
}
