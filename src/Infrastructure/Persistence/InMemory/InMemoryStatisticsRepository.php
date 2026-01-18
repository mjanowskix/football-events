<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\InMemory;

use App\Domain\Entity\FoulEvent;
use App\Domain\Entity\GoalEvent;
use App\Domain\Repository\StatisticsRepositoryInterface;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\TeamId;

final class InMemoryStatisticsRepository implements StatisticsRepositoryInterface
{
    public function __construct(
        private readonly InMemoryEventRepository $eventRepository,
    ) {
    }

    public function getTeamStatistics(MatchId $matchId, TeamId $teamId): array
    {
        $events = $this->eventRepository->findByMatchAndTeam($matchId, $teamId);

        $goals = 0;
        $fouls = 0;

        foreach ($events as $event) {
            if ($event instanceof GoalEvent) {
                ++$goals;
            } elseif ($event instanceof FoulEvent) {
                ++$fouls;
            }
        }

        return ['goals' => $goals, 'fouls' => $fouls];
    }

    public function getMatchStatistics(MatchId $matchId): array
    {
        $events = $this->eventRepository->findByMatch($matchId);

        $result = [];

        foreach ($events as $event) {
            $teamId = $event->teamId()->value();

            if (!isset($result[$teamId])) {
                $result[$teamId] = ['goals' => 0, 'fouls' => 0];
            }

            if ($event instanceof GoalEvent) {
                ++$result[$teamId]['goals'];
            } elseif ($event instanceof FoulEvent) {
                ++$result[$teamId]['fouls'];
            }
        }

        return $result;
    }
}
