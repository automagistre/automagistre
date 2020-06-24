<?php

declare(strict_types=1);

namespace App\Order\Entity;

use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface Discounted
{
    public function isDiscounted(): bool;

    public function discount(?Money $discount = null): ?Money;
}
