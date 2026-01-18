<?php

declare(strict_types=1);

namespace App\Application\DTO;

/**
 * Response DTO for statistics queries.
 */
final readonly class StatisticsResponse
{
    /**
     * @param array<string, mixed> $statistics
     */
    private function __construct(
        public string $matchId,
        public ?string $teamId,
        public array $statistics,
    ) {
    }

    /**
     * @param array{goals: int, fouls: int} $statistics
     */
    public static function forTeam(string $matchId, string $teamId, array $statistics): self
    {
        return new self($matchId, $teamId, $statistics);
    }

    /**
     * @param array<string, array{goals: int, fouls: int}> $statistics
     */
    public static function forMatch(string $matchId, array $statistics): self
    {
        return new self($matchId, null, $statistics);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'match_id' => $this->matchId,
            'statistics' => $this->statistics,
        ];

        if ($this->teamId !== null) {
            $result['team_id'] = $this->teamId;
        }

        return $result;
    }
}
