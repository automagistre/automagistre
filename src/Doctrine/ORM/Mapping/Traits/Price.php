<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping\Traits;

use Doctrine\ORM\Mapping as ORM;
use function is_numeric;
use Money\Currency;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait Price
{
    /**
     * @var Money|null
     *
     * @ORM\Embedded(class="Money\Money")
     */
    private $price;

    public function getPrice(): Money
    {
        if (null === $this->price || !is_numeric($this->price->getAmount())) {
            $this->price = new Money(0, new Currency('RUB'));
        }

        return $this->price;
    }
}
