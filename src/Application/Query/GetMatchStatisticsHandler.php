<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\DTO\StatisticsResponse;
use App\Domain\Repository\StatisticsRepositoryInterface;
use App\Domain\ValueObject\MatchId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetMatchStatisticsHandler
{
    public function __construct(
        private StatisticsRepositoryInterface $statisticsRepository,
    ) {
    }

    public function __invoke(GetMatchStatisticsQuery $query): StatisticsResponse
    {
        $matchId = MatchId::fromString($query->matchId);
        $statistics = $this->statisticsRepository->getMatchStatistics($matchId);

        return StatisticsResponse::forMatch($query->matchId, $statistics);
    }
}
