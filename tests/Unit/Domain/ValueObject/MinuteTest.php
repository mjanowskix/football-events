<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\Minute;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MinuteTest extends TestCase
{
    #[Test]
    public function itCreatesWithMinuteAndSecond(): void
    {
        $minute = Minute::create(45, 30);

        self::assertSame(45, $minute->minute());
        self::assertSame(30, $minute->second());
    }

    #[Test]
    public function itDefaultsSecondToZero(): void
    {
        $minute = Minute::create(90);

        self::assertSame(90, $minute->minute());
        self::assertSame(0, $minute->second());
    }

    #[Test]
    #[DataProvider('invalidMinuteProvider')]
    public function itThrowsOnInvalidMinute(int $minute): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Minute must be between 0 and 120');

        Minute::create($minute);
    }

    public static function invalidMinuteProvider(): array
    {
        return [
            'negative' => [-1],
            'too high' => [121],
        ];
    }

    #[Test]
    #[DataProvider('invalidSecondProvider')]
    public function itThrowsOnInvalidSecond(int $second): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Second must be between 0 and 59');

        Minute::create(45, $second);
    }

    public static function invalidSecondProvider(): array
    {
        return [
            'negative' => [-1],
            'too high' => [60],
        ];
    }

    #[Test]
    public function itCalculatesTotalSeconds(): void
    {
        $minute = Minute::create(45, 30);

        self::assertSame(2730, $minute->totalSeconds());
    }

    #[Test]
    public function itFormatsToString(): void
    {
        $minute = Minute::create(45, 5);

        self::assertSame("45'05\"", $minute->format());
        self::assertSame("45'05\"", (string) $minute);
    }

    #[Test]
    public function itComparesEquality(): void
    {
        $m1 = Minute::create(45, 30);
        $m2 = Minute::create(45, 30);
        $m3 = Minute::create(45, 31);

        self::assertTrue($m1->equals($m2));
        self::assertFalse($m1->equals($m3));
    }
}
