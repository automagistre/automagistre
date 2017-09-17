<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Identity;
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
 *     "1" = "App\Entity\OrderItemService",
 *     "2" = "App\Entity\OrderItemPart",
 *     "3" = "App\Entity\OrderItemGroup"
 * })
 */
abstract class OrderItem
{
    use Identity;

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
     * @var OrderItem
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\OrderItem", inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct(Order $order)
    {
        $this->createdAt = new \DateTime();
        $this->children = new ArrayCollection();

        $this->order = $order;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getParent(): ?OrderItem
    {
        return $this->parent;
    }

    public function setParent(OrderItem $item = null): void
    {
        $this->parent = $item;
    }

    public function getLevel(): int
    {
        return $this->parent ? $this->parent->getLevel() + 1 : 0;
    }

    /**
     * @return OrderItem[]
     */
    public function getChildren(): array
    {
        return $this->children->toArray();
    }

    public function getCreatedAt(): \DateTime
    {
        return clone $this->createdAt;
    }

    protected function getTotalPriceByClass(string $class, OrderItem $item = null): Money
    {
        $price = new Money(0, new Currency('RUB'));

        if ($item instanceof $class) {
            if ($item instanceof TotalPriceInterface) {
                $price = $price->add($item->getTotalPrice());
            } elseif ($item instanceof PriceInterface) {
                $price = $price->add($item->getPrice());
            }
        }

        foreach ($item ? $item->getChildren() : $this->children as $child) {
            $price = $price->add($this->getTotalPriceByClass($class, $child));
        }

        return $price;
    }
}
