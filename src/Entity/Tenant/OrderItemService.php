<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\Price;
use App\Doctrine\ORM\Mapping\Traits\Uuid;
use App\Doctrine\ORM\Mapping\Traits\Warranty;
use App\Entity\Embeddable\OperandRelation;
use App\Entity\Landlord\Operand;
use App\Entity\Landlord\User;
use App\Entity\WarrantyInterface;
use App\Money\PriceInterface;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Money\Money;

/**
 * @ORM\Entity
 */
class OrderItemService extends OrderItem implements PriceInterface, WarrantyInterface
{
    use Uuid;
    use Price;
    use Warranty;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $service;

    /**
     * @var OperandRelation
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\OperandRelation")
     */
    private $worker;

    public function __construct(Order $order, string $service, Money $price, User $user, Operand $worker = null)
    {
        parent::__construct($order, $user);

        $this->generateUuid();

        $this->service = $service;
        $this->price = $price;
        $this->worker = new OperandRelation($worker);
    }

    public function __toString(): string
    {
        return $this->getService();
    }

    public function setPrice(Money $price): void
    {
        if (!$this->getOrder()->isEditable()) {
            throw new DomainException('Can\'t change price on service on closed order.');
        }

        $this->price = $price;
    }

    public function setService(?string $service): void
    {
        $this->service = $service;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getWorker(): ?Operand
    {
        return $this->worker->entityOrNull();
    }

    public function setWorker(?Operand $worker): void
    {
        if (!$this->getOrder()->isEditable()) {
            throw new DomainException('Can\'t change order service worker on closed order.');
        }

        $this->worker = new OperandRelation($worker);
    }

    public function getTotalPartPrice(): Money
    {
        return $this->getTotalPriceByClass(OrderItemPart::class);
    }
}
