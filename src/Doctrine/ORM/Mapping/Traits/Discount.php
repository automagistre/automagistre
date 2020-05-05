<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping\Traits;

use Doctrine\ORM\Mapping as ORM;
use function is_numeric;
use LogicException;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait Discount
{
    /**
     * @ORM\Embedded(class=Money::class)
     */
    private ?Money $discount = null;

    public function isDiscounted(): bool
    {
        if (null === $this->discount) {
            return false;
        }

        // Doctrine create nullable embedded
        if (!is_numeric($this->discount->getAmount())) {
            return false;
        }

        return !$this->discount->isZero();
    }

    public function discount(?Money $discount = null): Money
    {
        if (null === $discount && null === $this->discount) {
            throw new LogicException('Discount not defined.');
        }

        if (null === $discount) {
            return $this->discount;
        }

        return $this->discount = $discount->absolute();
    }
}
