<?php

declare(strict_types=1);

namespace App\Domain\Event;

enum EventType: string
{
    case GOAL = 'goal';
    case FOUL = 'foul';

    public function label(): string
    {
        return match ($this) {
            self::GOAL => 'Goal',
            self::FOUL => 'Foul',
        };
    }
}
