---
title: Cache
---

# Cache

Parsing and rendering markdown on every request would be slow, so the package caches all rendered files.

The flip side: when you edit a markdown file, you won't see the change until the cache is cleared:

```bash
php artisan cache:clear
```

## Configuration

The cache prefix and TTL (in seconds) can be changed in the config file, or via the environment:

```php
'cache' => [
    'prefix' => env('FILAMENT_KB_CACHE_PREFIX', 'filament_kb_'),
    'ttl' => env('FILAMENT_KB_CACHE_TTL', 86400),
],
```
