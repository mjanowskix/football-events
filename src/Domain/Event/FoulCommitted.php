<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\PlayerId;
use App\Domain\ValueObject\TeamId;
use DateTimeImmutable;

/**
 * Domain event emitted when a foul is committed.
 */
final readonly class FoulCommitted implements DomainEvent
{
    public function __construct(
        private EventId $eventId,
        private MatchId $matchId,
        private TeamId $teamId,
        private PlayerId $playerAtFault,
        private ?PlayerId $affectedPlayer,
        private DateTimeImmutable $occurredAt,
    ) {
    }

    public function eventId(): EventId
    {
        return $this->eventId;
    }

    public function matchId(): MatchId
    {
        return $this->matchId;
    }

    public function teamId(): TeamId
    {
        return $this->teamId;
    }

    public function playerAtFault(): PlayerId
    {
        return $this->playerAtFault;
    }

    public function affectedPlayer(): ?PlayerId
    {
        return $this->affectedPlayer;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
