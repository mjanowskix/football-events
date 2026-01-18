<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Type;

use App\Domain\ValueObject\Minute;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Stores Minute as integer: minute * 100 + second (e.g., 45:30 -> 4530).
 */
final class MinuteType extends Type
{
    public const NAME = 'minute';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Minute
    {
        if ($value === null) {
            return null;
        }

        $intValue = (int) $value;
        $minute = intdiv($intValue, 100);
        $second = $intValue % 100;

        return Minute::create($minute, $second);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Minute) {
            return $value->minute() * 100 + $value->second();
        }

        return (int) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
