<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Price;
use App\Money\PriceInterface;
use App\Money\TotalPriceInterface;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class OrderItemPart extends OrderItem implements PriceInterface, TotalPriceInterface
{
    use Price;

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

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn
     */
    private $selector;

    public function __construct(Order $order, User $selector, Part $part, int $quantity, Money $price)
    {
        parent::__construct($order);

        $this->part = $part;
        $this->quantity = $quantity;
        $this->changePrice($price);
        $this->selector = $selector;
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
            throw new \DomainException('Can\'t change price on part on closed order.');
        }

        $this->changePrice($price);
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotalPrice(): Money
    {
        return $this->getPrice()->multiply($this->getQuantity() / 100);
    }

    public function getSelector(): User
    {
        return $this->selector;
    }
}
