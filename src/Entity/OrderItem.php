<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedBy;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Money\PriceInterface;
use App\Money\TotalPriceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="integer")
 * @ORM\DiscriminatorMap({
 *     "1": "App\Entity\OrderItemService",
 *     "2": "App\Entity\OrderItemPart",
 *     "3": "App\Entity\OrderItemGroup"
 * })
 */
abstract class OrderItem
{
    use Identity;
    use CreatedAt;
    use CreatedBy;

    /**
     * @var OrderItem[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", mappedBy="parent", cascade={"persist"}, orphanRemoval=true)
     */
    protected $children;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="items")
     */
    private $order;

    /**
     * @var OrderItem|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\OrderItem", inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    public function __construct(Order $order, User $user)
    {
        $this->children = new ArrayCollection();

        $this->order = $order;
        $this->createdBy = $user;
    }

    abstract public function __toString(): string;

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(self $item = null): void
    {
        $this->parent = $item;
    }

    public function getLevel(): int
    {
        return $this->parent instanceof self ? $this->parent->getLevel() + 1 : 0;
    }

    /**
     * @return OrderItem[]
     */
    public function getChildren(): array
    {
        return $this->children->toArray();
    }

    protected function getTotalPriceByClass(string $class, self $item = null): Money
    {
        $price = new Money(0, new Currency('RUB'));

        if ($item instanceof $class) {
            if ($item instanceof TotalPriceInterface) {
                $price = $price->add($item->getTotalPrice());
            } elseif ($item instanceof PriceInterface) {
                $price = $price->add($item->getPrice());
            }
        }

        foreach ($item instanceof self ? $item->getChildren() : $this->children as $child) {
            $price = $price->add($this->getTotalPriceByClass($class, $child));
        }

        return $price;
    }
}
