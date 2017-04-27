<?php

namespace App\Money;

use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface TotalPriceInterface
{
    public function getTotalPrice(): Money;
}
