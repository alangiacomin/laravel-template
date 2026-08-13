<?php

use AlanGiacomin\LaravelCqrs\LaravelCqrsServiceProvider;
use App\Infrastructure\Providers\AppServiceProvider;
use App\Infrastructure\Providers\EventServiceProvider;

return [
    LaravelCqrsServiceProvider::class,
    AppServiceProvider::class,
    EventServiceProvider::class,
];
