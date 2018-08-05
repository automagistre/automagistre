<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping\Traits;

use Doctrine\ORM\Mapping as ORM;
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

    public function getPrice(): ?Money
    {
        if (null === $this->price) {
            return null;
        }

        return is_numeric($this->price->getAmount()) ? $this->price : null;
    }

    public function setPrice(Money $money): void
    {
        $this->changePrice($money);
    }

    public function setPrice(?Money $money): void
    {
        $this->price = $money;
    }
}
