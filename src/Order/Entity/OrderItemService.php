<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Keycloak\Entity\UserId;
use App\Customer\Entity\OperandId;
use App\Money\PriceInterface;
use App\Money\TotalPriceInterface;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use DateTimeImmutable;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class OrderItemService extends OrderItem implements PriceInterface, TotalPriceInterface, WarrantyInterface, Discounted
{
    /**
     * @ORM\Column
     */
    public string $service;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    private Money $price;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    private Money $discount;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?OperandId $workerId;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $warranty;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(
        UuidInterface $id,
        Order $order,
        string $service,
        Money $price,
        Money $discount = null,
        OperandId $workerId = null,
        bool $warranty = false,
        OrderItem $parent = null,
    ) {
        parent::__construct($id, $order, $parent);

        $this->service = $service;
        $this->price = $price;
        $this->discount = $discount ?? $price->multiply('0');
        $this->workerId = $workerId ?? $order->getWorkerPersonId();
        $this->warranty = $warranty;
    }

    public function __toString(): string
    {
        return $this->service;
    }

    public function setPrice(Money $price): void
    {
        if (!$this->getOrder()->isEditable()) {
            throw new DomainException('Can\'t change price on service on closed order.');
        }

        $this->price = $price;
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

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function isWarranty(): bool
    {
        return $this->warranty;
    }

    public function setWarranty(bool $warranty): void
    {
        $this->warranty = $warranty;
    }

    public function getTotalPartPrice(bool $withDiscount = false): Money
    {
        return $this->getTotalPriceByClass(OrderItemPart::class, $withDiscount);
    }

    public function getTotalPrice(bool $withDiscount = false, bool $considerWarranty = true): Money
    {
        $price = $this->getPrice();

        if ($considerWarranty && $this->isWarranty()) {
            return $price->multiply('0');
        }

        if ($withDiscount && $this->isDiscounted()) {
            $price = $price->subtract($this->discount());
        }

        return $price;
    }
}
