<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use DomainException;

/**
 * Exception thrown when an event is not found.
 */
final class EventNotFoundException extends DomainException
{
    public static function withId(string $id): self
    {
        return new self(\sprintf('Event with id "%s" not found', $id));
    }
}
