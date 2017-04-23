<?php

declare(strict_types=1);

namespace App\Money;

use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MoneyFormatter
{
    /**
     * @var \Money\MoneyFormatter
     */
    private $formatter;

    public function __construct(\Money\MoneyFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function format(Money $money/*, $locale = null*/): string
    {
        return $this->formatter->format($money);
    }
}
