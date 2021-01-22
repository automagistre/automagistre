<?php

declare(strict_types=1);

namespace App\Tests;

use App\Costil;
use Generator;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * Как я докатился до этой жизни.
 */
final class CostilTest extends TestCase
{
    /**
     * @dataProvider convertToMoneyData
     */
    public function testConvertToMoney(string $key): void
    {
        $array = Costil::convertToMoney([
            $key.'.amount' => 1000,
            $key.'.currency.code' => 'RUB',
        ]);

        self::assertCount(1, $array);
        self::assertArrayHasKey($key, $array);

        $money = $array[$key];
        self::assertInstanceOf(Money::class, $money);
        /** @var Money $money */
        $money->equals(new Money(1000, new Currency('RUB')));
    }

    public function convertToMoneyData(): Generator
    {
        yield ['price'];
        yield ['discount'];
    }
}
