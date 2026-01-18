<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\GoalEvent;
use App\Domain\Event\EventType;
use App\Domain\Event\GoalScored;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\Minute;
use App\Domain\ValueObject\PlayerId;
use App\Domain\ValueObject\TeamId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GoalEventTest extends TestCase
{
    #[Test]
    public function itCreatesGoalEvent(): void
    {
        $matchId = MatchId::fromString('match-1');
        $teamId = TeamId::fromString('arsenal');
        $scorer = PlayerId::fromString('saka');
        $minute = Minute::create(23, 15);

        $event = GoalEvent::create($matchId, $teamId, $scorer, null, $minute);

        self::assertTrue($event->matchId()->equals($matchId));
        self::assertTrue($event->teamId()->equals($teamId));
        self::assertTrue($event->scorer()->equals($scorer));
        self::assertNull($event->assistant());
        self::assertSame(EventType::GOAL, $event->type());
    }

    #[Test]
    public function itCreatesGoalEventWithAssistant(): void
    {
        $matchId = MatchId::fromString('match-1');
        $teamId = TeamId::fromString('arsenal');
        $scorer = PlayerId::fromString('saka');
        $assistant = PlayerId::fromString('odegaard');
        $minute = Minute::create(23);

        $event = GoalEvent::create($matchId, $teamId, $scorer, $assistant, $minute);

        self::assertNotNull($event->assistant());
        self::assertTrue($event->assistant()->equals($assistant));
    }

    #[Test]
    public function itRecordsDomainEvent(): void
    {
        $event = GoalEvent::create(
            MatchId::fromString('match-1'),
            TeamId::fromString('arsenal'),
            PlayerId::fromString('saka'),
            null,
            Minute::create(23),
        );

        $domainEvents = $event->pullDomainEvents();

        self::assertCount(1, $domainEvents);
        self::assertInstanceOf(GoalScored::class, $domainEvents[0]);
    }

    #[Test]
    public function itClearsDomainEventsAfterPull(): void
    {
        $event = GoalEvent::create(
            MatchId::fromString('match-1'),
            TeamId::fromString('arsenal'),
            PlayerId::fromString('saka'),
            null,
            Minute::create(23),
        );

        $event->pullDomainEvents();
        $secondPull = $event->pullDomainEvents();

        self::assertCount(0, $secondPull);
    }

    #[Test]
    public function itConvertsToArray(): void
    {
        $event = GoalEvent::create(
            MatchId::fromString('match-1'),
            TeamId::fromString('arsenal'),
            PlayerId::fromString('saka'),
            PlayerId::fromString('odegaard'),
            Minute::create(23, 15),
        );

        $array = $event->toArray();

        self::assertSame('match-1', $array['match_id']);
        self::assertSame('arsenal', $array['team_id']);
        self::assertSame('saka', $array['scorer']);
        self::assertSame('odegaard', $array['assistant']);
        self::assertSame(23, $array['minute']);
        self::assertSame(15, $array['second']);
        self::assertSame('goal', $array['type']);
    }
}
