<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\CreatedByRelation as CreatedBy;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Discounted;
use App\Entity\Embeddable\CarRelation;
use App\Entity\Embeddable\OperandRelation;
use App\Entity\Embeddable\UserRelation;
use App\Entity\Landlord\Car;
use App\Entity\Landlord\Operand;
use App\Entity\Landlord\User;
use App\Entity\WarrantyInterface;
use App\Enum\OrderStatus;
use App\Money\TotalPriceInterface;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use LogicException;
use Money\Currency;
use Money\Money;

/**
 * @ORM\Table(
 *     name="orders",
 *     indexes={@ORM\Index(name="SEARCH_IDX", columns={"closed_at"})}
 * )
 * @ORM\Entity
 */
class Order
{
    use Identity;
    use CreatedAt;
    use CreatedBy;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Tenant\OrderItem", mappedBy="order", cascade={"persist"},
     * orphanRemoval=true)
     */
    private $items;

    /**
     * @var DateTimeImmutable|null
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $closedAt;

    /**
     * @var UserRelation
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\UserRelation")
     */
    private $closedBy;

    /**
     * @var Money|null
     *
     * @ORM\Embedded(class="Money\Money")
     */
    private $closedBalance;

    /**
     * @var OrderStatus
     *
     * @ORM\Column(name="status", type="order_status_enum")
     */
    private $status;

    /**
     * @var CarRelation
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\CarRelation")
     */
    private $car;

    /**
     * @var OperandRelation
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\OperandRelation")
     */
    private $customer;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", length=8, nullable=true, options={"unsigned": true})
     */
    private $mileage;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Tenant\OrderPayment", mappedBy="order", cascade={"persist"})
     */
    private $payments;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Tenant\OrderSuspend", mappedBy="order", cascade={"persist", "remove"})
     */
    private $suspends;

    /**
     * @var DateTimeImmutable|null
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $appointmentAt;

    public function __construct()
    {
        $this->status = OrderStatus::working();
        $this->car = new CarRelation();
        $this->customer = new OperandRelation();
        $this->items = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->suspends = new ArrayCollection();
        $this->closedBy = new UserRelation();
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }

    public function isDiscounted(): bool
    {
        return $this->isPartsDiscounted() || $this->isServicesDiscounted();
    }

    public function discount(): ?Money
    {
        $discount = null;
        if ($this->isPartsDiscounted()) {
            $discount = $this->partsDiscount();
        }

        if ($this->isServicesDiscounted()) {
            $discount = $discount instanceof Money
                ? $discount->add($this->servicesDiscount())
                : $this->servicesDiscount();
        }

        return $discount;
    }

    public function isPartsDiscounted(): bool
    {
        return $this->isDiscountedByClass(OrderItemPart::class);
    }

    public function isServicesDiscounted(): bool
    {
        return $this->isDiscountedByClass(OrderItemService::class);
    }

    public function partsDiscount(): Money
    {
        return $this->discountByClass(OrderItemPart::class);
    }

    public function servicesDiscount(): Money
    {
        return $this->discountByClass(OrderItemService::class);
    }

    /**
     * @return OrderItemService[]
     */
    public function getServicesWithoutWorker(): array
    {
        return $this->items->filter(function (OrderItem $item) {
            return $item instanceof OrderItemService && null === $item->getWorker();
        })->getValues();
    }

    public function close(User $user, ?Money $balance): void
    {
        $this->status = OrderStatus::closed();
        $this->closedBy = new UserRelation($user);
        $this->closedAt = new DateTimeImmutable();
        $this->closedBalance = $balance;
    }

    public function appointment(DateTimeImmutable $appointment): void
    {
        $this->appointmentAt = $appointment;
    }

    public function getActiveWorker(): ?Operand
    {
        foreach ($this->getItems(OrderItemService::class) as $service) {
            /** @var OrderItemService $service */
            $worker = $service->getWorker();

            if (null !== $worker) {
                return $worker;
            }
        }

        return null;
    }

    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
    }

    public function getItems(string $class = null): array
    {
        if (null === $class) {
            return $this->items->toArray();
        }

        return $this->items->filter(function (OrderItem $item) use ($class) {
            return $item instanceof $class;
        })->getValues();
    }

    /**
     * @return OrderItem[]
     */
    public function getRootItems(): array
    {
        if (!$this->items instanceof Selectable) {
            throw new LogicException(\sprintf('Collection must implement "%s"', Selectable::class));
        }

        $criteria = Criteria::create()->where(Criteria::expr()->isNull('parent'));

        return $this->items->matching($criteria)->toArray();
    }

    public function getCar(): ?Car
    {
        return $this->car->entityOrNull();
    }

    public function setCar(?Car $car): void
    {
        $this->car = new CarRelation($car);

        if ($car instanceof Car) {
            $customer = $car->getOwner();
            if ($customer instanceof Operand && $this->customer->isEmpty()) {
                $this->customer = new OperandRelation($customer);
            }
        }
    }

    public function getCustomer(): ?Operand
    {
        return $this->customer->entityOrNull();
    }

    public function setCustomer(?Operand $customer): void
    {
        $this->customer = new OperandRelation($customer);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function getClosedBy(): ?User
    {
        return $this->closedBy->entityOrNull();
    }

    public function getClosedBalance(): ?Money
    {
        return $this->closedBalance;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(?int $mileage): void
    {
        $this->mileage = $mileage;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): void
    {
        if ($status->isClosed()) {
            throw new DomainException('Can\'t close order with setStatus()');
        }

        $this->status = $status;
    }

    public function getTotalGroupPrice(bool $withDiscount = false): Money
    {
        $money = new Money(0, new Currency('RUB'));

        foreach ($this->getRootItems() as $item) {
            if (!$item instanceof OrderItemGroup) {
                continue;
            }

            $money = $money->add($item->getTotalServicePrice($withDiscount));

            if ($item->isHideParts()) {
                $money = $money->add($item->getTotalPartPrice($withDiscount));
            }
        }

        return $money;
    }

    public function getTotalPrice(bool $withDiscount = false): Money
    {
        return $this->getTotalPriceByClass(null, $withDiscount);
    }

    public function getTotalServicePrice(bool $withDiscount = false, bool $excludeGroups = false): Money
    {
        $money = new Money(0, new Currency('RUB'));

        /** @var OrderItemService $item */
        foreach ($this->getItems(OrderItemService::class) as $item) {
            if ($excludeGroups && null !== $item->getParent()) {
                continue;
            }

            $money = $money->add($item->getTotalPrice($withDiscount));
        }

        return $money;
    }

    public function getTotalPartPrice(bool $withDiscount = false, bool $excludeHidden = false): Money
    {
        $money = new Money(0, new Currency('RUB'));

        /** @var OrderItemPart $item */
        foreach ($this->getItems(OrderItemPart::class) as $item) {
            if ($excludeHidden && $item->isHidden()) {
                continue;
            }

            $money = $money->add($item->getTotalPrice($withDiscount));
        }

        return $money;
    }

    public function isEditable(): bool
    {
        return $this->getStatus()->isEditable();
    }

    public function isReadyToClose(): bool
    {
        return $this->isEditable()
            && [] === $this->getServicesWithoutWorker()
            && ($this->car->isEmpty() || null !== $this->mileage);
    }

    public function isClosed(): bool
    {
        return $this->status->isClosed();
    }

    public function getTotalPayments(): Money
    {
        $money = new Money(0, new Currency('RUB'));
        foreach ($this->payments as $payment) {
            $money = $money->add($payment->getMoney());
        }

        return $money;
    }

    public function getTotalForPayment(Money $balance = null): Money
    {
        $forPayment = (new Money(0, new Currency('RUB')))
            ->add($this->getTotalPartPrice(true))
            ->add($this->getTotalServicePrice(true))
            ->subtract($this->getTotalPayments());

        if ($balance instanceof Money) {
            $forPayment = $forPayment->add($balance->multiply(-1));
        }

        return $forPayment;
    }

    public function addPayment(Money $money, ?string $description, User $user): void
    {
        $this->payments[] = new OrderPayment($this, $money, $description, $user);
    }

    /**
     * @return OrderPayment[]
     */
    public function getPayments(): array
    {
        return $this->payments->toArray();
    }

    public function isSuspended(): bool
    {
        if ($this->suspends->isEmpty()) {
            return false;
        }

        return $this->getLastSuspend()->getTill() > new DateTime();
    }

    public function getLastSuspend(): OrderSuspend
    {
        return $this->suspends->last();
    }

    public function getSuspends(): array
    {
        return $this->suspends->getValues();
    }

    public function suspend(DateTimeImmutable $till, string $reason, User $user): void
    {
        $this->suspends[] = new OrderSuspend($this, $till, $reason, $user);
    }

    public function getAppointmentAt(): ?DateTimeImmutable
    {
        return $this->appointmentAt;
    }

    private function isDiscountedByClass(string $class): bool
    {
        foreach ($this->getItems($class) as $item) {
            if ($item instanceof Discounted && $item->isDiscounted()) {
                return true;
            }
        }

        return false;
    }

    private function discountByClass(string $class): Money
    {
        $discount = null;
        foreach ($this->getItems($class) as $item) {
            if ($item instanceof Discounted && $item->isDiscounted()) {
                $itemDiscount = $item->discount();
                if ($item instanceof OrderItemPart) {
                    $itemDiscount = $itemDiscount->multiply($item->getQuantity() / 100);
                }

                $discount = $discount instanceof Money ? $discount->add($itemDiscount) : $itemDiscount;
            }
        }

        if (null === $discount) {
            throw new LogicException(\sprintf('Discount for class "%s" is null', $class));
        }

        return $discount;
    }

    private function getTotalPriceByClass(?string $class, bool $withDiscount = false): Money
    {
        $price = new Money(0, new Currency('RUB'));

        foreach ($this->getItems($class) as $item) {
            if ($item instanceof WarrantyInterface && $item->isWarranty()) {
                continue;
            }

            if ($item instanceof TotalPriceInterface) {
                $price = $price->add($item->getTotalPrice($withDiscount));
            } elseif ($item instanceof OrderItemGroup) {
                continue;
            } else {
                throw new DomainException('Can\'t calculate total price for item which not have price');
            }
        }

        return $price;
    }
}
