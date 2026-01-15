<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final readonly class MatchId
{
    private function __construct(
        private string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if (empty($value)) {
            throw new InvalidArgumentException('MatchId cannot be empty');
        }

        if (\strlen($value) > 100) {
            throw new InvalidArgumentException('MatchId cannot be longer than 100 characters');
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
