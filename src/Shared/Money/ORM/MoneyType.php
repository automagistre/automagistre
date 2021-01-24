<?php

declare(strict_types=1);

namespace App\Shared\Money\ORM;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Money\Currency;
use Money\Money;
use function assert;
use function explode;
use function sprintf;

final class MoneyType extends Type
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        assert($value instanceof Money);

        return sprintf('%s %s', $value->getCurrency()->getCode(), $value->getAmount());
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        [$currency, $amount] = explode(' ', $value);

        return new Money($amount, new Currency($currency));
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return 'money';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
