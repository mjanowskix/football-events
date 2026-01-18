<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Event\EventType;
use App\Domain\Repository\StatisticsRepositoryInterface;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\TeamId;
use Doctrine\DBAL\Connection;

final readonly class DoctrineStatisticsRepository implements StatisticsRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function getTeamStatistics(MatchId $matchId, TeamId $teamId): array
    {
        $sql = '
            SELECT 
                type,
                COUNT(*) as count
            FROM events 
            WHERE match_id = :match_id AND team_id = :team_id
            GROUP BY type
        ';

        $rows = $this->connection->fetchAllAssociative($sql, [
            'match_id' => $matchId->value(),
            'team_id' => $teamId->value(),
        ]);

        return $this->mapToStatistics($rows);
    }

    public function getMatchStatistics(MatchId $matchId): array
    {
        $sql = '
            SELECT 
                team_id,
                type,
                COUNT(*) as count
            FROM events 
            WHERE match_id = :match_id
            GROUP BY team_id, type
        ';

        $rows = $this->connection->fetchAllAssociative($sql, [
            'match_id' => $matchId->value(),
        ]);

        $result = [];
        foreach ($rows as $row) {
            $teamId = $row['team_id'];
            if (!isset($result[$teamId])) {
                $result[$teamId] = ['goals' => 0, 'fouls' => 0];
            }

            if ($row['type'] === EventType::GOAL->value) {
                $result[$teamId]['goals'] = (int) $row['count'];
            } elseif ($row['type'] === EventType::FOUL->value) {
                $result[$teamId]['fouls'] = (int) $row['count'];
            }
        }

        return $result;
    }

    /**
     * @param list<array<string, mixed>> $rows
     *
     * @return array{goals: int, fouls: int}
     */
    private function mapToStatistics(array $rows): array
    {
        $stats = ['goals' => 0, 'fouls' => 0];

        foreach ($rows as $row) {
            if ($row['type'] === EventType::GOAL->value) {
                $stats['goals'] = (int) $row['count'];
            } elseif ($row['type'] === EventType::FOUL->value) {
                $stats['fouls'] = (int) $row['count'];
            }
        }

        return $stats;
    }
}
