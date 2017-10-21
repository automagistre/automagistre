<?php

declare(strict_types=1);

namespace App\Symfony;

use Money\MoneyFormatter as Formatter;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait MoneyFormatter
{
    /**
     * @var Formatter
     */
    protected $moneyFormatter;

    /**
     * @required
     */
    public function setMoneyFormatter(Formatter $moneyFormatter): void
    {
        $this->moneyFormatter = $moneyFormatter;
    }
}
