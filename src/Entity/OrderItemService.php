<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Price;
use App\Money\PriceInterface;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class OrderItemService extends OrderItem implements PriceInterface
{
    use Price;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $service;

    /**
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
     * @ORM\JoinColumn
     */
    private $worker;

    public function __construct(Order $order, string $service, Money $price)
    {
        parent::__construct($order);

        $this->service = $service;
        $this->changePrice($price);
    }

    public function __toString(): string
    {
        return $this->getService();
    }

    public function setPrice(Money $price): void
    {
        if (!$this->getOrder()->isEditable()) {
            throw new \DomainException('Can\'t change price on service on closed order.');
        }

        $this->changePrice($price);
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
        return $this->worker;
    }

    public function setWorker(?Operand $worker): void
    {
        if (!$this->getOrder()->isEditable()) {
            throw new \DomainException('Can\'t change order service worker on closed order.');
        }

        $this->worker = $worker;
    }

    public function getTotalPartPrice(): Money
    {
        return $this->getTotalPriceByClass(OrderItemPart::class);
    }
}
