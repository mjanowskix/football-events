<?php

declare(strict_types=1);

namespace App\Domain\Event;

use DateTimeImmutable;

/**
 * Base interface for all domain events.
 * Domain events represent something that happened in the domain.
 */
interface DomainEvent
{
    public function occurredAt(): DateTimeImmutable;
}
