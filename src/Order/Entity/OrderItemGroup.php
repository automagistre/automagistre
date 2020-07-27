<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Shared\Money\TotalPriceInterface;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Generator;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class OrderItemGroup extends OrderItem implements TotalPriceInterface
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

    public function __construct(UuidInterface $id, Order $order, string $name)
    {
        parent::__construct($id, $order);

        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getTotalPrice(bool $withDiscount = false): Money
    {
        return $this->getTotalServicePrice($withDiscount)->add($this->getTotalPartPrice($withDiscount));
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

    /**
     * @psalm-return Generator<int, OrderItemPart>
     */
    public function getParts(): Generator
    {
        foreach ($this->children as $child) {
            if ($child instanceof OrderItemPart) {
                yield $child;

                continue;
            }

            if ($child instanceof OrderItemService) {
                foreach ($child->getChildren() as $item) {
                    if ($item instanceof OrderItemPart) {
                        yield $item;
                    }
                }
            }
        }
    }
}
