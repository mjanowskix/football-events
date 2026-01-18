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
    /** @var array<array{matchId: string, event: DomainEvent}> */
    private array $notifications = [];

    public function __construct(
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function notify(DomainEvent $event): void
    {
        $this->notifications[] = ['matchId' => '', 'event' => $event];

        $this->logger->info('Domain event dispatched', [
            'event_class' => $event::class,
            'occurred_at' => $event->occurredAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function notifyMatch(string $matchId, DomainEvent $event): void
    {
        $this->notifications[] = ['matchId' => $matchId, 'event' => $event];

        $this->logger->info('Domain event dispatched for match', [
            'match_id' => $matchId,
            'event_class' => $event::class,
            'occurred_at' => $event->occurredAt()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get all notifications (useful for testing).
     *
     * @return array<array{matchId: string, event: DomainEvent}>
     */
    public function getNotifications(): array
    {
        return $this->notifications;
    }

    /**
     * Clear notifications (useful for testing).
     */
    public function clear(): void
    {
        $this->notifications = [];
    }
}
