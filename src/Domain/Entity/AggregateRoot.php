<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Event\DomainEvent;

/**
 * Base class for aggregate roots that can emit domain events.
 */
abstract class AggregateRoot
{
    /** @var DomainEvent[] */
    private array $recordedEvents = [];

    protected function recordEvent(DomainEvent $event): void
    {
        $this->recordedEvents[] = $event;
    }

    /**
     * @return DomainEvent[]
     */
    public function pullDomainEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    public function hasDomainEvents(): bool
    {
        return \count($this->recordedEvents) > 0;
    }
}
