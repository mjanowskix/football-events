<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\TeamId;

/**
 * Repository interface for match/team statistics.
 */
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

    /**
     * Increment goals counter for a team.
     */
    public function incrementGoals(MatchId $matchId, TeamId $teamId): void;

    /**
     * Increment fouls counter for a team.
     */
    public function incrementFouls(MatchId $matchId, TeamId $teamId): void;
}
