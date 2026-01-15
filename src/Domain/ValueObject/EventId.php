<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

final readonly class EventId
{
    private function __construct(
        private string $value,
    ) {
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $value): self
    {
        if (empty($value)) {
            throw new InvalidArgumentException('EventId cannot be empty');
        }

        if (!Uuid::isValid($value)) {
            throw new InvalidArgumentException('EventId must be a valid UUID');
        }

        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
