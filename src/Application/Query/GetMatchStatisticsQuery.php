<?php

declare(strict_types=1);

namespace App\Application\Query;

/**
 * Query to get statistics for all teams in a match.
 */
final readonly class GetMatchStatisticsQuery
{
    public function __construct(
        public string $matchId,
    ) {
    }
}
