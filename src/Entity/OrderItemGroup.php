<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Money\Money;

/**
 * @ORM\Entity
 */
class OrderItemGroup extends OrderItem
{
    /**
     * @var string
     *
     * @ORM\Column
     */
    private $name;

    public function __construct(Order $order, string $name)
    {
        parent::__construct($order);

        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function setName(string $name): void
    {
        if (!$this->getOrder()->isEditable()) {
            throw new DomainException('Can\'t change group name on closed order');
        }

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTotalPartPrice(): Money
    {
        return $this->getTotalPriceByClass(OrderItemPart::class);
    }

    public function getTotalServicePrice(): Money
    {
        return $this->getTotalPriceByClass(OrderItemService::class);
    }
}
