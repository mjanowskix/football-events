<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Event\EventType;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\Minute;
use App\Domain\ValueObject\TeamId;
use DateTimeImmutable;

/**
 * Base abstract class for all football events.
 */
abstract class FootballEvent extends AggregateRoot
{
    protected EventId $id;

    protected MatchId $matchId;

    protected TeamId $teamId;

    protected Minute $minute;

    protected DateTimeImmutable $occurredAt;

    protected DateTimeImmutable $createdAt;

    public function id(): EventId
    {
        return $this->id;
    }

    public function matchId(): MatchId
    {
        return $this->matchId;
    }

    public function teamId(): TeamId
    {
        return $this->teamId;
    }

    public function minute(): Minute
    {
        return $this->minute;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    abstract public function type(): EventType;

    /**
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;
}
