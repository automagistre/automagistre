<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Costil;
use App\Customer\Entity\Operand;
use App\Entity\Discounted;
use App\Entity\Embeddable\OperandRelation;
use App\Entity\WarrantyInterface;
use App\Part\Entity\PartId;
use App\Shared\Doctrine\ORM\Mapping\Traits\Discount;
use App\Shared\Doctrine\ORM\Mapping\Traits\Price;
use App\Shared\Doctrine\ORM\Mapping\Traits\Warranty;
use App\Shared\Money\PriceInterface;
use App\Shared\Money\TotalPriceInterface;
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
     * @ORM\Column(type="part_id")
     */
    private PartId $partId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function __construct(Order $order, PartId $partId, int $quantity, Money $price)
    {
        parent::__construct($order);

        $this->supplier = new OperandRelation();
        $this->partId = $partId;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public function __toString(): string
    {
        return Costil::display($this->partId);
    }

    public function isHidden(): bool
    {
        $group = $this->getParent();
        if ($group instanceof OrderItemService) {
            $group = $group->getParent();
        }

        return $group instanceof OrderItemGroup && $group->isHideParts();
    }

    public function getPartId(): PartId
    {
        return $this->partId;
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
