<?php

declare(strict_types=1);

namespace App\Money\ORM;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Money\Currency;
use Money\Money;
use function assert;
use function explode;
use function is_numeric;
use function sprintf;

final class MoneyType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        assert($value instanceof Money);

        return sprintf('%s %s', $value->getCurrency()->getCode(), $value->getAmount());
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Money
    {
        if (null === $value) {
            return new Money(0, new Currency('RUB'));
        }

        /**
         * @psalm-var non-empty-string $currency
         */
        [$currency, $amount] = explode(' ', $value);

        assert(is_numeric($amount));

        return new Money($amount, new Currency($currency));
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'money';
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
