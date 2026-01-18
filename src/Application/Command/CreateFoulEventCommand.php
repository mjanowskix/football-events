<?php

declare(strict_types=1);

namespace App\Application\Command;

/**
 * Command to create a foul event.
 */
final readonly class CreateFoulEventCommand
{
    public function __construct(
        public string $matchId,
        public string $teamId,
        public string $player,
        public ?string $affectedPlayer,
        public int $minute,
        public int $second = 0,
    ) {
    }
}
