<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Costil;
use App\Customer\Entity\OperandId;
use App\Part\Entity\PartId;
use App\PartPrice\PartPrice;
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
     * @ORM\Column(type="operand_id", nullable=true)
     */
    private ?OperandId $supplierId = null;

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

    public function __construct(Order $order, PartId $partId, int $quantity)
    {
        parent::__construct($order);

        $this->partId = $partId;
        $this->quantity = $quantity;
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

    public function setPrice(Money $price, PartPrice $partPrice): void
    {
        if (!$this->getOrder()->isEditable()) {
            throw new LogicException('Can\'t change price on part on closed order.');
        }

        $priceFromCatalog = $partPrice->price($this->partId);
        $discount = $priceFromCatalog->subtract($price);

        if ($discount->isPositive()) {
            $price = $priceFromCatalog;
        }

        $this->price = $price;
        $this->discount = $discount->isPositive() ? $discount : null;
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

    public function getSupplierId(): ?OperandId
    {
        return $this->supplierId;
    }

    public function setSupplierId(?OperandId $supplierId): void
    {
        $this->supplierId = $supplierId;
    }
}
