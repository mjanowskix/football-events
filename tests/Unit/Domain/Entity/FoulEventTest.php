<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\FoulEvent;
use App\Domain\Event\EventType;
use App\Domain\Event\FoulCommitted;
use App\Domain\ValueObject\MatchId;
use App\Domain\ValueObject\Minute;
use App\Domain\ValueObject\PlayerId;
use App\Domain\ValueObject\TeamId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FoulEventTest extends TestCase
{
    #[Test]
    public function itCreatesFoulEvent(): void
    {
        $matchId = MatchId::fromString('match-1');
        $teamId = TeamId::fromString('chelsea');
        $playerAtFault = PlayerId::fromString('silva');
        $minute = Minute::create(45, 30);

        $event = FoulEvent::create($matchId, $teamId, $playerAtFault, null, $minute);

        self::assertTrue($event->matchId()->equals($matchId));
        self::assertTrue($event->teamId()->equals($teamId));
        self::assertTrue($event->playerAtFault()->equals($playerAtFault));
        self::assertNull($event->affectedPlayer());
        self::assertSame(EventType::FOUL, $event->type());
    }

    #[Test]
    public function itCreatesFoulEventWithAffectedPlayer(): void
    {
        $matchId = MatchId::fromString('match-1');
        $teamId = TeamId::fromString('chelsea');
        $playerAtFault = PlayerId::fromString('silva');
        $affectedPlayer = PlayerId::fromString('saka');
        $minute = Minute::create(45);

        $event = FoulEvent::create($matchId, $teamId, $playerAtFault, $affectedPlayer, $minute);

        self::assertNotNull($event->affectedPlayer());
        self::assertTrue($event->affectedPlayer()->equals($affectedPlayer));
    }

    #[Test]
    public function itRecordsDomainEvent(): void
    {
        $event = FoulEvent::create(
            MatchId::fromString('match-1'),
            TeamId::fromString('chelsea'),
            PlayerId::fromString('silva'),
            null,
            Minute::create(45),
        );

        $domainEvents = $event->pullDomainEvents();

        self::assertCount(1, $domainEvents);
        self::assertInstanceOf(FoulCommitted::class, $domainEvents[0]);
    }

    #[Test]
    public function itConvertsToArray(): void
    {
        $event = FoulEvent::create(
            MatchId::fromString('match-1'),
            TeamId::fromString('chelsea'),
            PlayerId::fromString('silva'),
            PlayerId::fromString('saka'),
            Minute::create(45, 30),
        );

        $array = $event->toArray();

        self::assertSame('match-1', $array['match_id']);
        self::assertSame('chelsea', $array['team_id']);
        self::assertSame('silva', $array['player']);
        self::assertSame('saka', $array['affected_player']);
        self::assertSame(45, $array['minute']);
        self::assertSame(30, $array['second']);
        self::assertSame('foul', $array['type']);
    }
}
