<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Query;

use App\Application\DTO\StatisticsResponse;
use App\Application\Query\GetMatchStatisticsHandler;
use App\Application\Query\GetMatchStatisticsQuery;
use App\Domain\Entity\FoulEvent;
use App\Domain\Entity\GoalEvent;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\Minute;
use App\Domain\ValueObject\PlayerId;
use App\Domain\ValueObject\TeamId;
use App\Infrastructure\Persistence\InMemory\InMemoryEventRepository;
use App\Infrastructure\Persistence\InMemory\InMemoryStatisticsRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GetMatchStatisticsHandlerTest extends TestCase
{
    private InMemoryEventRepository $eventRepository;

    private InMemoryStatisticsRepository $statisticsRepository;

    private GetMatchStatisticsHandler $handler;

    protected function setUp(): void
    {
        $this->eventRepository = new InMemoryEventRepository();
        $this->statisticsRepository = new InMemoryStatisticsRepository($this->eventRepository);
        $this->handler = new GetMatchStatisticsHandler($this->statisticsRepository);
    }

    #[Test]
    public function itReturnsEmptyStatisticsForNewMatch(): void
    {
        $query = new GetMatchStatisticsQuery('match-1');

        $response = ($this->handler)($query);

        self::assertInstanceOf(StatisticsResponse::class, $response);
        self::assertSame('match-1', $response->matchId);
        self::assertEmpty($response->statistics);
    }

    #[Test]
    public function itReturnsStatisticsForMatchWithEvents(): void
    {
        // Add events
        $matchId = MatchId::fromString('match-1');

        $this->eventRepository->save(GoalEvent::create(
            $matchId,
            TeamId::fromString('arsenal'),
            PlayerId::fromString('saka'),
            null,
            Minute::create(23),
        ));

        $this->eventRepository->save(FoulEvent::create(
            $matchId,
            TeamId::fromString('chelsea'),
            PlayerId::fromString('silva'),
            null,
            Minute::create(45),
        ));

        $query = new GetMatchStatisticsQuery('match-1');
        $response = ($this->handler)($query);

        self::assertArrayHasKey('arsenal', $response->statistics);
        self::assertArrayHasKey('chelsea', $response->statistics);
        self::assertSame(1, $response->statistics['arsenal']['goals']);
        self::assertSame(0, $response->statistics['arsenal']['fouls']);
        self::assertSame(0, $response->statistics['chelsea']['goals']);
        self::assertSame(1, $response->statistics['chelsea']['fouls']);
    }

    #[Test]
    public function itAggregatesMultipleEventsPerTeam(): void
    {
        $matchId = MatchId::fromString('match-1');
        $teamId = TeamId::fromString('arsenal');

        $this->eventRepository->save(GoalEvent::create(
            $matchId, $teamId, PlayerId::fromString('saka'), null, Minute::create(10),
        ));
        $this->eventRepository->save(GoalEvent::create(
            $matchId, $teamId, PlayerId::fromString('odegaard'), null, Minute::create(30),
        ));
        $this->eventRepository->save(FoulEvent::create(
            $matchId, $teamId, PlayerId::fromString('rice'), null, Minute::create(45),
        ));

        $query = new GetMatchStatisticsQuery('match-1');
        $response = ($this->handler)($query);

        self::assertSame(2, $response->statistics['arsenal']['goals']);
        self::assertSame(1, $response->statistics['arsenal']['fouls']);
    }
}
