<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\DTO\StatisticsResponse;
use App\Domain\Repository\StatisticsRepositoryInterface;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\TeamId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetTeamStatisticsHandler
{
    public function __construct(
        private StatisticsRepositoryInterface $statisticsRepository,
    ) {
    }

    public function __invoke(GetTeamStatisticsQuery $query): StatisticsResponse
    {
        $matchId = MatchId::fromString($query->matchId);
        $teamId = TeamId::fromString($query->teamId);
        $statistics = $this->statisticsRepository->getTeamStatistics($matchId, $teamId);

        return StatisticsResponse::forTeam($query->matchId, $query->teamId, $statistics);
    }
}
