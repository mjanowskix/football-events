<?php

declare(strict_types=1);

namespace App\Application\Query;

/**
 * Query to get statistics for a specific team in a match.
 */
final readonly class GetTeamStatisticsQuery
{
    public function __construct(
        public string $matchId,
        public string $teamId,
    ) {
    }
}
