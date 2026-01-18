<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\TeamId;

interface StatisticsRepositoryInterface
{
    /**
     * Get statistics for a specific team in a match.
     *
     * @return array{goals: int, fouls: int}
     */
    public function getTeamStatistics(MatchId $matchId, TeamId $teamId): array;

    /**
     * Get statistics for all teams in a match.
     *
     * @return array<string, array{goals: int, fouls: int}>
     */
    public function getMatchStatistics(MatchId $matchId): array;
}
