<?php

namespace App\Areas\Main\Auth\Infrastructure\Listeners\UserRegistered;

use App\Areas\Main\Auth\Domain\Events\UserRegisteredEvent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcasts real-time notification to frontend when a Todo is completed.
 * Uses Laravel Reverb WebSocket connection.
 */
class Broadcast implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Queue name for broadcasting operations
     */
    // public string $queue = 'broadcasts';

    private UserRegisteredEvent $domainEvent;

    /**
     * Handle the event.
     */
    public function handle(UserRegisteredEvent $event): void
    {
        // Log::info('Broadcasting todo completato', [
        //     'todo_id' => $event->todoId,
        // ]);

        // Salva l'evento per usarlo nei metodi di broadcast
        $this->domainEvent = $event;

        // Disponi sè stesso come evento broadcastable
        event($this);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channel = 'App.Models.User.'.$this->domainEvent->user->id;
        $channel = new PrivateChannel($channel);
        // $channel = new Channel('public-channel');

        return [
            $channel,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'utente.registrato';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            // 'todo_id' => $this->domainEvent->todoId,
            // 'nota' => $this->domainEvent->nota,
            // 'completed_at' => $this->domainEvent->occurredAt,
            'test' => 'qui broadcast',
            // ...$this->domainEvent->getMetadata(),
        ];
    }

    public function broadcastWhen(): bool
    {
        return false;
    }
}
