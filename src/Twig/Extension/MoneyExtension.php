<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class MoneyExtension extends \Twig_Extension
{
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('localize_money', [$this, 'localizeMoney']),
        ];
    }

    public function localizeMoney(Money $money, $locale = null): string
    {
        $numberFormatter = new \NumberFormatter($locale ?: 'ru', \NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        return $moneyFormatter->format($money);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_money';
    }
}
