<?php

namespace App\Infrastructure\Providers;

use Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * Listeners are organized by event in their own namespace folder.
     * Each event has all its listeners grouped together.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected array $listen = [
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }
}
