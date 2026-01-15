<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\FootballEvent;
use App\Domain\ValueObject\EventId;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\TeamId;

/**
 * Repository interface for football events.
 */
interface EventRepositoryInterface
{
    public function save(FootballEvent $event): void;

    public function findById(EventId $id): ?FootballEvent;

    /**
     * @return FootballEvent[]
     */
    public function findByMatch(MatchId $matchId): array;

    /**
     * @return FootballEvent[]
     */
    public function findByMatchAndTeam(MatchId $matchId, TeamId $teamId): array;

    /**
     * @return FootballEvent[]
     */
    public function findAll(): array;
}
