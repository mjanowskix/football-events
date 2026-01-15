<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Event\EventType;
use App\Domain\Event\FoulCommitted;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\Minute;
use App\Domain\ValueObject\PlayerId;
use App\Domain\ValueObject\TeamId;
use DateTimeImmutable;

/**
 * Represents a foul committed during a football match.
 */
final class FoulEvent extends FootballEvent
{
    private PlayerId $playerAtFault;

    private ?PlayerId $affectedPlayer;

    private function __construct(
        EventId $id,
        MatchId $matchId,
        TeamId $teamId,
        PlayerId $playerAtFault,
        ?PlayerId $affectedPlayer,
        Minute $minute,
        DateTimeImmutable $occurredAt,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->matchId = $matchId;
        $this->teamId = $teamId;
        $this->playerAtFault = $playerAtFault;
        $this->affectedPlayer = $affectedPlayer;
        $this->minute = $minute;
        $this->occurredAt = $occurredAt;
        $this->createdAt = $createdAt;
    }

    public static function create(
        MatchId $matchId,
        TeamId $teamId,
        PlayerId $playerAtFault,
        ?PlayerId $affectedPlayer,
        Minute $minute,
        ?DateTimeImmutable $occurredAt = null,
    ): self {
        $id = EventId::generate();
        $now = new DateTimeImmutable();
        $occurredAt = $occurredAt ?? $now;

        $event = new self(
            $id,
            $matchId,
            $teamId,
            $playerAtFault,
            $affectedPlayer,
            $minute,
            $occurredAt,
            $now,
        );

        // Record domain event
        $event->recordEvent(new FoulCommitted(
            $id,
            $matchId,
            $teamId,
            $playerAtFault,
            $affectedPlayer,
            $occurredAt,
        ));

        return $event;
    }

    /**
     * Factory for reconstituting from persistence (no domain events).
     */
    public static function reconstitute(
        EventId $id,
        MatchId $matchId,
        TeamId $teamId,
        PlayerId $playerAtFault,
        ?PlayerId $affectedPlayer,
        Minute $minute,
        DateTimeImmutable $occurredAt,
        DateTimeImmutable $createdAt,
    ): self {
        return new self(
            $id,
            $matchId,
            $teamId,
            $playerAtFault,
            $affectedPlayer,
            $minute,
            $occurredAt,
            $createdAt,
        );
    }

    public function playerAtFault(): PlayerId
    {
        return $this->playerAtFault;
    }

    public function affectedPlayer(): ?PlayerId
    {
        return $this->affectedPlayer;
    }

    public function type(): EventType
    {
        return EventType::FOUL;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'type' => $this->type()->value,
            'match_id' => $this->matchId->value(),
            'team_id' => $this->teamId->value(),
            'player' => $this->playerAtFault->value(),
            'affected_player' => $this->affectedPlayer?->value(),
            'minute' => $this->minute->minute(),
            'second' => $this->minute->second(),
            'occurred_at' => $this->occurredAt->format(DateTimeImmutable::ATOM),
            'created_at' => $this->createdAt->format(DateTimeImmutable::ATOM),
        ];
    }
}
