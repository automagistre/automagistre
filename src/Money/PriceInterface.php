<?php

namespace App\Money;

use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface PriceInterface
{
    public function getPrice(): Money;
}
