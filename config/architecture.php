<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Areas
    |--------------------------------------------------------------------------
    |
    | List of areas to scan for auto-discovery features like repositories
    | and event listeners. Areas follow the Domain-Driven Design structure.
    |
    */

    'areas' => [
        'Main',
        'Admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Repository Auto-Discovery
    |--------------------------------------------------------------------------
    |
    | Configuration for automatic repository binding. When enabled, the system
    | will scan all areas for repositories following the pattern:
    | - Interface: *\Domain\Repositories\I*Repository (extends IRepository)
    | - Implementation: *\Infrastructure\Repositories\*Repository
    |
    */

    'repositories' => [
        // Enable/disable auto-discovery of repositories
        'auto_discover' => env('REPOSITORY_AUTO_DISCOVER', true),

        // Enable caching in production for better performance
        'cache_bindings' => env('REPOSITORY_CACHE_BINDINGS', true),

        // Cache TTL in seconds (default: 24 hours)
        'cache_ttl' => env('REPOSITORY_CACHE_TTL', 86400),

        // Cache key for storing bindings
        'cache_key' => 'app.repositories.bindings',
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Listener Auto-Discovery
    |--------------------------------------------------------------------------
    |
    | Configuration for automatic event listener registration. When enabled,
    | the system will scan all areas for listeners following the pattern:
    | - Listeners: *\Domain\Listeners\*\*.php
    |
    */

    'events' => [
        // Enable/disable auto-discovery of event listeners
        'auto_discover' => env('EVENT_AUTO_DISCOVER', true),
    ],
];
