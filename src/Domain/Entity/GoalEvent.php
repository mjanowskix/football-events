<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Event\EventType;
use App\Domain\Event\GoalScored;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\Minute;
use App\Domain\ValueObject\PlayerId;
use App\Domain\ValueObject\TeamId;
use DateTimeImmutable;

/**
 * Represents a goal scored during a football match.
 */
final class GoalEvent extends FootballEvent
{
    private PlayerId $scorer;

    private ?PlayerId $assistant;

    private function __construct(
        EventId $id,
        MatchId $matchId,
        TeamId $teamId,
        PlayerId $scorer,
        ?PlayerId $assistant,
        Minute $minute,
        DateTimeImmutable $occurredAt,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->matchId = $matchId;
        $this->teamId = $teamId;
        $this->scorer = $scorer;
        $this->assistant = $assistant;
        $this->minute = $minute;
        $this->occurredAt = $occurredAt;
        $this->createdAt = $createdAt;
    }

    public static function create(
        MatchId $matchId,
        TeamId $teamId,
        PlayerId $scorer,
        ?PlayerId $assistant,
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
            $scorer,
            $assistant,
            $minute,
            $occurredAt,
            $now,
        );

        // Record domain event
        $event->recordEvent(new GoalScored(
            $id,
            $matchId,
            $teamId,
            $scorer,
            $assistant,
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
        PlayerId $scorer,
        ?PlayerId $assistant,
        Minute $minute,
        DateTimeImmutable $occurredAt,
        DateTimeImmutable $createdAt,
    ): self {
        return new self(
            $id,
            $matchId,
            $teamId,
            $scorer,
            $assistant,
            $minute,
            $occurredAt,
            $createdAt,
        );
    }

    public function scorer(): PlayerId
    {
        return $this->scorer;
    }

    public function assistant(): ?PlayerId
    {
        return $this->assistant;
    }

    public function type(): EventType
    {
        return EventType::GOAL;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'type' => $this->type()->value,
            'match_id' => $this->matchId->value(),
            'team_id' => $this->teamId->value(),
            'scorer' => $this->scorer->value(),
            'assistant' => $this->assistant?->value(),
            'minute' => $this->minute->minute(),
            'second' => $this->minute->second(),
            'occurred_at' => $this->occurredAt->format(DateTimeImmutable::ATOM),
            'created_at' => $this->createdAt->format(DateTimeImmutable::ATOM),
        ];
    }
}
