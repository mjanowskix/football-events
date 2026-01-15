<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use DomainException;

/**
 * Exception thrown when event data is invalid.
 */
final class InvalidEventDataException extends DomainException
{
    public static function missingField(string $field): self
    {
        return new self(\sprintf('Missing required field: %s', $field));
    }

    public static function invalidType(string $type): self
    {
        return new self(\sprintf('Invalid event type: %s', $type));
    }

    public static function invalidValue(string $field, string $reason): self
    {
        return new self(\sprintf('Invalid value for field "%s": %s', $field, $reason));
    }
}
