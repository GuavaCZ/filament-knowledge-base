<?php

if (! function_exists('docs_path')) {
    /**
     * Get the path to the docs folder.
     */
    function docs_path(string $path = ''): string
    {
        return app()->joinPaths(base_path('docs'), $path);
    }
}
