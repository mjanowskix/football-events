<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Event\DomainEvent;

/**
 * Interface for notifying clients about domain events.
 */
interface EventNotifierInterface
{
    /**
     * Notify all connected clients about a domain event.
     */
    public function notify(DomainEvent $event): void;

    /**
     * Notify clients subscribed to a specific match.
     */
    public function notifyMatch(string $matchId, DomainEvent $event): void;
}
