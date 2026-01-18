<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\MatchId;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MatchIdTest extends TestCase
{
    #[Test]
    public function itCreatesFromValidString(): void
    {
        $matchId = MatchId::fromString('match-123');

        self::assertSame('match-123', $matchId->value());
    }

    #[Test]
    public function itTrimsWhitespace(): void
    {
        $matchId = MatchId::fromString('  match-123  ');

        self::assertSame('match-123', $matchId->value());
    }

    #[Test]
    public function itThrowsOnEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('MatchId cannot be empty');

        MatchId::fromString('');
    }

    #[Test]
    public function itThrowsOnWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);

        MatchId::fromString('   ');
    }

    #[Test]
    public function itThrowsOnTooLongString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('MatchId cannot be longer than 100 characters');

        MatchId::fromString(str_repeat('a', 101));
    }

    #[Test]
    public function itComparesEquality(): void
    {
        $id1 = MatchId::fromString('match-123');
        $id2 = MatchId::fromString('match-123');
        $id3 = MatchId::fromString('match-456');

        self::assertTrue($id1->equals($id2));
        self::assertFalse($id1->equals($id3));
    }
}
