<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use morphos\Russian\MoneySpeller;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MoneyExtension extends AbstractExtension
{
    private MoneyFormatter $formatter;

    private DecimalMoneyFormatter $decimalMoneyFormatter;

    public function __construct(MoneyFormatter $formatter, DecimalMoneyFormatter $decimalMoneyFormatter)
    {
        $this->formatter = $formatter;
        $this->decimalMoneyFormatter = $decimalMoneyFormatter;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('localize_money', [$this, 'localizeMoney']),
            new TwigFilter('localize_money_literal', [$this, 'literal']),
        ];
    }

    public function localizeMoney(Money $money, bool $amountOnly = false): string
    {
        if ($amountOnly) {
            return $this->decimalMoneyFormatter->format($money);
        }

        return $this->formatter->format($money);
    }

    public function literal(Money $money): string
    {
        $float = (float) $this->decimalMoneyFormatter->format($money);

        return MoneySpeller::spell($float, $money->getCurrency()->getCode());
    }
}
