<?php

declare(strict_types=1);

namespace App\Application\Command;

/**
 * Command to create a goal event.
 */
final readonly class CreateGoalEventCommand
{
    public function __construct(
        public string $matchId,
        public string $teamId,
        public string $scorer,
        public ?string $assistant,
        public int $minute,
        public int $second = 0,
    ) {
    }
}
