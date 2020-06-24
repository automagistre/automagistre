<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Entity\Discounted;
use App\Shared\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Shared\Doctrine\ORM\Mapping\Traits\CreatedBy;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use App\Shared\Money\PriceInterface;
use App\Shared\Money\TotalPriceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "1": "App\Order\Entity\OrderItemService",
 *     "2": "App\Order\Entity\OrderItemPart",
 *     "3": "App\Order\Entity\OrderItemGroup"
 * })
 */
abstract class OrderItem
{
    use Identity;
    use CreatedAt;
    use CreatedBy;

    public const MAP = [
        'group' => OrderItemGroup::class,
        'service' => OrderItemService::class,
        'part' => OrderItemPart::class,
    ];

    /**
     * @var Collection<int, OrderItem>
     *
     * @ORM\OneToMany(
     *     targetEntity=OrderItem::class,
     *     mappedBy="parent",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     * @ORM\OrderBy({"createdAt": "ASC"})
     */
    protected $children;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="items")
     */
    private $order;

    /**
     * @var OrderItem|null
     *
     * @ORM\ManyToOne(targetEntity=OrderItem::class, inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    public function __construct(Order $order)
    {
        $this->children = new ArrayCollection();

        $this->order = $order;
    }

    abstract public function __toString(): string;

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this instanceof PriceInterface && $this instanceof Discounted) {
            if ($this->isDiscounted() && $this->getPrice()->isZero()) {
                $context->buildViolation('Стоимость не может быть равно нулю при наличии скидки.')
                    ->atPath('price')
                    ->addViolation();
            }

            if ($this->isDiscounted() && $this->getPrice()->lessThan($this->discount())) {
                $context->buildViolation('Стоимость не может быть меньше скидки.')
                    ->atPath('price')
                    ->addViolation();
            }
        }
    }

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

    protected function getTotalPriceByClass(string $class, bool $withDiscount = false, self $item = null): Money
    {
        $price = new Money(0, new Currency('RUB'));

        if ($item instanceof $class && $item instanceof TotalPriceInterface) {
            $price = $price->add($item->getTotalPrice($withDiscount));
        }

        /** @noinspection ProperNullCoalescingOperatorUsageInspection */
        $item = $item ?? $this;
        foreach ($item->getChildren() as $child) {
            $price = $price->add($this->getTotalPriceByClass($class, $withDiscount, $child));
        }

        return $price;
    }
}
