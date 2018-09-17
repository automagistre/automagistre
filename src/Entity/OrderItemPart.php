<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Price;
use App\Doctrine\ORM\Mapping\Traits\Warranty;
use App\Money\PriceInterface;
use App\Money\TotalPriceInterface;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Money\Money;

/**
 * @ORM\Entity
 */
class OrderItemPart extends OrderItem implements PriceInterface, TotalPriceInterface, WarrantyInterface
{
    use Price;
    use Warranty;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Part")
     * @ORM\JoinColumn
     */
    private $part;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function __construct(Order $order, Part $part, int $quantity, Money $price, ?User $selector)
    {
        parent::__construct($order, $selector);

        $this->part = $part;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public function __toString(): string
    {
        return (string) $this->getPart()->getName();
    }

    public function getPart(): Part
    {
        return $this->part;
    }

    public function setPrice(Money $price): void
    {
        if (!$this->getOrder()->isEditable()) {
            throw new LogicException('Can\'t change price on part on closed order.');
        }

        $this->price = $price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getTotalPrice(): Money
    {
        return $this->getPrice()->multiply($this->getQuantity() / 100);
    }
}
