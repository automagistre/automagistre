<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Entity\Landlord\User;
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

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $hideParts = false;

    public function __construct(Order $order, string $name, User $user)
    {
        parent::__construct($order, $user);

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

    public function isHideParts(): bool
    {
        return $this->hideParts;
    }

    public function setHideParts(bool $hideParts): void
    {
        $this->hideParts = $hideParts;
    }

    public function getTotalPartPrice(bool $withDiscount = false): Money
    {
        return $this->getTotalPriceByClass(OrderItemPart::class, $withDiscount);
    }

    public function getTotalServicePrice(bool $withDiscount = false): Money
    {
        return $this->getTotalPriceByClass(OrderItemService::class, $withDiscount);
    }
}
