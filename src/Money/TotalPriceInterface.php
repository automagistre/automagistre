<?php

declare(strict_types=1);

namespace App\Money;

use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface TotalPriceInterface
{
    public function getTotalPrice(bool $withDiscount = false): Money;
}
