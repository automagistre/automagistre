<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait Price
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $price = 0;

    /**
     * @var string
     *
     * @ ORM\Column()
     */
    private $currency = 'RUB';

    public function getPrice(): Money
    {
        return new Money($this->price, new Currency($this->currency));
    }

    protected function changePrice(Money $money): void
    {
        $this->price = (int) $money->getAmount();
        $this->currency = $money->getCurrency()->getCode();
    }
}
