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
use App\Money\PriceInterface;
use App\Money\TotalPriceInterface;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use LogicException;
use Money\Currency;
use Money\Money;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity
 */
class Order
{
    use Identity;
    use CreatedAt;
    use CreatedBy;

    /**
     * @var OrderItem[]|ArrayCollection
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
     * @var int
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
     * @var OrderPayment[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Tenant\OrderPayment", mappedBy="order", cascade={"persist"})
     */
    private $payments;

    /**
     * @var ArrayCollection|OrderSuspend[]
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

    public function discount(): Money
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

    public function getTotalPrice(): Money
    {
        return $this->getTotalPriceByClass(null);
    }

    public function getTotalServicePrice(): Money
    {
        return $this->getTotalPriceByClass(OrderItemService::class);
    }

    public function getTotalPartPrice(): Money
    {
        return $this->getTotalPriceByClass(OrderItemPart::class);
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
            ->add($this->getTotalPartPrice())
            ->add($this->getTotalServicePrice())
            ->subtract($this->getTotalPayments());

        if ($this->isPartsDiscounted()) {
            $forPayment = $forPayment->subtract($this->partsDiscount());
        }

        if ($this->isServicesDiscounted()) {
            $forPayment = $forPayment->subtract($this->servicesDiscount());
        }

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
                $discount = $discount instanceof Money ? $discount->add($item->discount()) : $item->discount();
            }
        }

        if (null === $discount) {
            throw new LogicException(\sprintf('Discount for class "%s" is null', $class));
        }

        return $discount;
    }

    private function getTotalPriceByClass(?string $class): Money
    {
        $price = new Money(0, new Currency('RUB'));

        foreach ($this->getItems($class) as $item) {
            if ($item instanceof WarrantyInterface && $item->isWarranty()) {
                continue;
            }

            if ($item instanceof TotalPriceInterface) {
                $price = $price->add($item->getTotalPrice());
            } elseif ($item instanceof PriceInterface) {
                $price = $price->add($item->getPrice());
            } elseif ($item instanceof OrderItemGroup) {
                continue;
            } else {
                throw new DomainException('Can\'t calculate total price for item which not have price');
            }
        }

        return $price;
    }
}
