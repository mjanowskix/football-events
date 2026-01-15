<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final readonly class Minute
{
    private function __construct(
        private int $minute,
        private int $second,
    ) {
    }

    public static function create(int $minute, int $second = 0): self
    {
        if ($minute < 0 || $minute > 120) {
            throw new InvalidArgumentException('Minute must be between 0 and 120');
        }

        if ($second < 0 || $second > 59) {
            throw new InvalidArgumentException('Second must be between 0 and 59');
        }

        return new self($minute, $second);
    }

    public function minute(): int
    {
        return $this->minute;
    }

    public function second(): int
    {
        return $this->second;
    }

    public function totalSeconds(): int
    {
        return ($this->minute * 60) + $this->second;
    }

    public function format(): string
    {
        return \sprintf("%d'%02d\"", $this->minute, $this->second);
    }

    public function equals(self $other): bool
    {
        return $this->minute === $other->minute && $this->second === $other->second;
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
