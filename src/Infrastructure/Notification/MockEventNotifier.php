<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Event\DomainEvent;
use App\Domain\Service\EventNotifierInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Mock implementation of EventNotifier for development and testing.
 *
 * In production, this would be replaced with a real implementation
 * using Mercure, WebSocket, or other real-time communication mechanism.
 */
final class MockEventNotifier implements EventNotifierInterface
{
    /** @var DomainEvent[] */
    private array $notifiedEvents = [];

    public function __construct(
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function notify(DomainEvent $event): void
    {
        $this->notifiedEvents[] = $event;

        $this->logger->info('Domain event dispatched', [
            'event_class' => $event::class,
            'occurred_at' => $event->occurredAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function notifyMatch(string $matchId, DomainEvent $event): void
    {
        $this->notifiedEvents[] = $event;

        $this->logger->info('Domain event dispatched for match', [
            'match_id' => $matchId,
            'event_class' => $event::class,
            'occurred_at' => $event->occurredAt()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get all notified events (useful for testing).
     *
     * @return DomainEvent[]
     */
    public function getNotifiedEvents(): array
    {
        return $this->notifiedEvents;
    }

    /**
     * Clear notified events (useful for testing).
     */
    public function clear(): void
    {
        $this->notifiedEvents = [];
    }
}
