<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Money\Currency;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait EntityPriceTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=19, nullable=true)
     */
    private $amount;

    /**
     * @var Currency|null
     *
     * @ORM\Column(type="currency", nullable=true)
     */
    private $currency;

    public function getPrice(): ?Money
    {
        if (null === $this->amount) {
            return null;
        }

        return new Money($this->amount, $this->currency);
    }

    public function setPrice(?Money $money): void
    {
        if (null === $money) {
            $this->currency = $this->amount = null;

            return;
        }

        $this->amount = $money->getAmount();
        $this->currency = $money->getCurrency();
    }
}
