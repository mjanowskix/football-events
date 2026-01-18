<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\InMemory;

use App\Domain\Entity\FootballEvent;
use App\Domain\Repository\EventRepositoryInterface;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\TeamId;

final class InMemoryEventRepository implements EventRepositoryInterface
{
    /** @var array<string, FootballEvent> */
    private array $events = [];

    public function save(FootballEvent $event): void
    {
        $this->events[$event->id()->value()] = $event;
    }

    public function findById(EventId $id): ?FootballEvent
    {
        return $this->events[$id->value()] ?? null;
    }

    public function findByMatch(MatchId $matchId): array
    {
        return array_values(array_filter(
            $this->events,
            fn (FootballEvent $event) => $event->matchId()->equals($matchId),
        ));
    }

    public function findByMatchAndTeam(MatchId $matchId, TeamId $teamId): array
    {
        return array_values(array_filter(
            $this->events,
            fn (FootballEvent $event) => $event->matchId()->equals($matchId) && $event->teamId()->equals($teamId),
        ));
    }

    public function findAll(): array
    {
        return array_values($this->events);
    }

    public function clear(): void
    {
        $this->events = [];
    }
}
