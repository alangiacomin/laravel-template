<?php

namespace App\Areas\Main\Auth\Infrastructure\Listeners\UserRegistered;

use App\Areas\Main\Auth\Domain\Events\UserRegisteredEvent;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmail implements ShouldQueue
{
    /**
     * Queue name for email operations
     */
    // public string $queue = 'emails';

    /**
     * Number of times to retry if failed
     */
    public int $tries = 3;

    /**
     * Handle the event.
     */
    public function handle(UserRegisteredEvent $event): void
    {
        $userModel = User::findOrFail($event->user->id);
        event(new Registered($userModel));
    }
}
