<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Customer\Entity\OperandId;
use App\Shared\Doctrine\ORM\Mapping\Traits\Discount;
use App\Shared\Doctrine\ORM\Mapping\Traits\Price;
use App\Shared\Doctrine\ORM\Mapping\Traits\Warranty;
use App\Shared\Money\PriceInterface;
use App\Shared\Money\TotalPriceInterface;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Money\Money;

/**
 * @ORM\Entity
 */
class OrderItemService extends OrderItem implements PriceInterface, TotalPriceInterface, WarrantyInterface, Discounted
{
    use Price;
    use Warranty;
    use Discount;

    /**
     * @ORM\Column
     */
    public string $service;

    /**
     * @ORM\Column(type="operand_id", nullable=true)
     */
    public ?OperandId $workerId;

    public function __construct(Order $order, string $service, Money $price, OperandId $workerId = null)
    {
        parent::__construct($order);

        $this->service = $service;
        $this->price = $price;
        $this->workerId = $workerId ?? $order->getWorkerPersonId();
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

    public function getTotalPartPrice(bool $withDiscount = false): Money
    {
        return $this->getTotalPriceByClass(OrderItemPart::class, $withDiscount);
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

        return $price;
    }
}
