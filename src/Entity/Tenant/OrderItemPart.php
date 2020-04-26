<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Customer\Domain\Operand;
use App\Doctrine\ORM\Mapping\Traits\Discount;
use App\Doctrine\ORM\Mapping\Traits\Price;
use App\Doctrine\ORM\Mapping\Traits\Warranty;
use App\Entity\Discounted;
use App\Entity\Embeddable\OperandRelation;
use App\Entity\Embeddable\PartRelation;
use App\Entity\Landlord\Part;
use App\Entity\WarrantyInterface;
use App\Money\PriceInterface;
use App\Money\TotalPriceInterface;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Money\Money;

/**
 * @ORM\Entity
 */
class OrderItemPart extends OrderItem implements PriceInterface, TotalPriceInterface, WarrantyInterface, Discounted
{
    use Price;
    use Warranty;
    use Discount;

    /**
     * @var OperandRelation
     *
     * @ORM\Embedded(class=OperandRelation::class)
     */
    private $supplier;

    /**
     * @var PartRelation
     *
     * @ORM\Embedded(class=PartRelation::class)
     */
    private $part;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function __construct(Order $order, Part $part, int $quantity, Money $price)
    {
        parent::__construct($order);

        $this->supplier = new OperandRelation();
        $this->part = new PartRelation($part);
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public function __toString(): string
    {
        return (string) $this->getPart()->getName();
    }

    public function isHidden(): bool
    {
        $group = $this->getParent();
        if ($group instanceof OrderItemService) {
            $group = $group->getParent();
        }

        return $group instanceof OrderItemGroup && $group->isHideParts();
    }

    public function getPart(): Part
    {
        return $this->part->entity();
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

    public function getTotalPrice(bool $withDiscount = false): Money
    {
        $price = $this->getPrice();

        if ($this->isWarranty()) {
            return $price->multiply(0);
        }

        if ($withDiscount && $this->isDiscounted()) {
            $price = $price->subtract($this->discount());
        }

        return $price->multiply($this->getQuantity() / 100);
    }

    public function getSupplier(): ?Operand
    {
        return $this->supplier->entityOrNull();
    }

    public function setSupplier(?Operand $supplier): void
    {
        $this->supplier = new OperandRelation($supplier);
    }
}
