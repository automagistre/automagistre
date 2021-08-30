<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Costil;
use App\Customer\Entity\OperandId;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Money\PriceInterface;
use App\Money\TotalPriceInterface;
use App\Order\Messages\OrderItemPartCreated;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class OrderItemPart extends OrderItem implements PriceInterface, TotalPriceInterface, WarrantyInterface, Discounted, ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Column(type="operand_id", nullable=true)
     */
    private ?OperandId $supplierId;

    /**
     * @ORM\Column(type="part_id")
     */
    private PartId $partId;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    private Money $price;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    private Money $discount;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $warranty;

    public function __construct(
        UuidInterface $id,
        ?OrderItem $parent,
        Order $order,
        PartId $partId,
        Money $price,
        PartView $partView,
        int $quantity,
        bool $warranty = false,
        ?OperandId $supplierId = null,
    ) {
        parent::__construct($id, $order, $parent);

        $this->partId = $partId;
        $this->quantity = $quantity;
        [$this->price, $this->discount] = $this->calculatePriceAndDiscount($price, $partView);
        $this->warranty = $warranty;
        $this->supplierId = $supplierId;

        $this->record(new OrderItemPartCreated($this->toId()));
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

    public function setPrice(Money $price, PartView $partView): void
    {
        if (!$this->getOrder()->isEditable()) {
            throw new LogicException('Can\'t change price on part on closed order.');
        }

        [$this->price, $this->discount] = $this->calculatePriceAndDiscount($price, $partView);
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function isDiscounted(): bool
    {
        return $this->discount->isPositive();
    }

    public function discount(?Money $discount = null): Money
    {
        if (null === $discount) {
            return $this->discount;
        }

        return $this->discount = $discount->absolute();
    }

    public function getTotalPrice(bool $withDiscount = false): Money
    {
        $price = $this->getPrice();

        if ($this->isWarranty()) {
            return $price->multiply('0');
        }

        if ($withDiscount && $this->isDiscounted()) {
            $price = $price->subtract($this->discount());
        }

        return $price->multiply((string) ($this->getQuantity() / 100));
    }

    public function getSupplierId(): ?OperandId
    {
        return $this->supplierId;
    }

    public function setSupplierId(?OperandId $supplierId): void
    {
        $this->supplierId = $supplierId;
    }

    public function isWarranty(): bool
    {
        return $this->warranty;
    }

    public function setWarranty(bool $guarantee): void
    {
        $this->warranty = $guarantee;
    }

    /**
     * @return array{0: Money, 1: Money}
     */
    private function calculatePriceAndDiscount(Money $price, PartView $partView): array
    {
        $priceFromCatalog = $partView->price;
        $discount = $priceFromCatalog->subtract($price);

        if ($discount->isPositive()) {
            $price = $priceFromCatalog;
        }

        return [$price, $discount->isPositive() ? $discount : $discount->multiply('0')];
    }
}
