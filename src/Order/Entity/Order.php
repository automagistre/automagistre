<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Car\Entity\CarId;
use App\Customer\Entity\OperandId;
use App\Employee\Entity\Employee;
use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Order\Enum\OrderSatisfaction;
use App\Order\Enum\OrderStatus;
use App\Shared\Money\TotalPriceInterface;
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
use function assert;
use function class_exists;
use function sprintf;

/**
 * @ORM\Entity
 * @ORM\Table(name="orders")
 */
class Order implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column(type="order_id")
     */
    private OrderId $id;

    /**
     * @ORM\Column(unique=true)
     */
    private string $number;

    /**
     * @var Collection<int, OrderItem>
     *
     * @ORM\OneToMany(
     *     targetEntity=OrderItem::class,
     *     mappedBy="order",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     * @ORM\OrderBy({"id": "ASC"})
     */
    private $items;

    /**
     * @ORM\OneToOne(targetEntity=OrderClose::class, mappedBy="order", cascade={"persist"})
     */
    private ?OrderClose $close = null;

    /**
     * @ORM\Column(name="status", type="order_status_enum")
     */
    private OrderStatus $status;

    /**
     * @ORM\Column(type="car_id", nullable=true)
     */
    private ?CarId $carId = null;

    /**
     * @ORM\Column(type="operand_id", nullable=true)
     */
    private ?OperandId $customerId = null;

    /**
     * @ORM\ManyToOne(targetEntity=Employee::class)
     */
    private ?Employee $worker = null;

    /**
     * @ORM\Column(type="integer", length=8, nullable=true, options={"unsigned": true})
     */
    private ?int $mileage = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @var Collection<int, OrderPayment>
     *
     * @ORM\OneToMany(targetEntity=OrderPayment::class, mappedBy="order", cascade={"persist"})
     * @ORM\OrderBy({"id": "ASC"})
     */
    private $payments;

    /**
     * @var Collection<int, OrderSuspend>
     *
     * @ORM\OneToMany(targetEntity=OrderSuspend::class, mappedBy="order", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id": "ASC"})
     */
    private $suspends;

    public function __construct(OrderId $orderId, string $number)
    {
        $this->id = $orderId;
        $this->number = $number;
        $this->status = OrderStatus::working();
        $this->items = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->suspends = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->toId();
    }

    public function toId(): OrderId
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
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
     * @return array<int, OrderItemService>
     */
    public function getServicesWithoutWorker(): array
    {
        /** @var Collection<int, OrderItemService> $collection */
        $collection = $this->items
            ->filter(fn ($item) => $item instanceof OrderItemService && null === $item->workerId)
        ;

        return $collection->getValues();
    }

    public function close(?Money $balance, OrderSatisfaction $satisfaction): void
    {
        if (null !== $this->close) {
            throw new DomainException(sprintf('Order "%s" already closed.', $this->toId()->toString()));
        }

        $this->status = OrderStatus::closed();

        $this->close = new OrderDeal(
            $this,
            $balance,
            $satisfaction,
        );
    }

    public function cancel(): void
    {
        if (null !== $this->close) {
            throw new DomainException(sprintf('Order "%s" already closed.', $this->toId()->toString()));
        }

        $this->status = OrderStatus::cancelled();

        $this->close = new OrderCancel(
            $this,
        );
    }

    public function getClose(): OrderClose
    {
        if (null === $this->close) {
            throw new LogicException('Order not closed to get OrderClose.');
        }

        return $this->close;
    }

    public function getWorkerPersonId(): ?OperandId
    {
        if (null === $this->worker) {
            return null;
        }

        return $this->worker->getPersonId();
    }

    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
    }

    public function getItems(string $class = null, bool $withoutHidden = false): array
    {
        if (null === $class) {
            return $this->items->toArray();
        }

        if (!class_exists($class)) {
            $class = OrderItem::MAP[$class];
        }

        return $this->items->filter(static function (OrderItem $item) use ($class, $withoutHidden): bool {
            if ($withoutHidden && $item instanceof OrderItemPart && $item->isHidden()) {
                return false;
            }

            return $item instanceof $class;
        })->toArray();
    }

    public function getRootItems(string $class = null): array
    {
        if (!$this->items instanceof Selectable) {
            throw new LogicException(sprintf('Collection must implement "%s"', Selectable::class));
        }

        $criteria = Criteria::create()->where(Criteria::expr()->isNull('parent'));

        $items = $this->items->matching($criteria);

        if (null === $class) {
            return $items->toArray();
        }

        if (!class_exists($class)) {
            $class = OrderItem::MAP[$class];
        }

        return $items->filter(static function (OrderItem $item) use ($class): bool {
            return $item instanceof $class;
        })->toArray();
    }

    public function getCarId(): ?CarId
    {
        return $this->carId;
    }

    public function setCarId(?CarId $carId): void
    {
        $this->carId = $carId;
    }

    public function getCustomerId(): ?OperandId
    {
        return $this->customerId;
    }

    public function setCustomerId(?OperandId $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getWorker(): ?Employee
    {
        return $this->worker;
    }

    public function setWorker(?Employee $worker): void
    {
        $previousId = null !== $this->worker ? $this->worker->getPersonId() : null;
        $this->worker = $worker;

        if (null === $worker || null === $worker->getPersonId()) {
            return;
        }

        $newWorkerId = $worker->getPersonId();

        foreach ($this->items->filter(fn (OrderItem $item) => $item instanceof OrderItemService) as $item) {
            assert($item instanceof OrderItemService);

            if (null === $item->workerId || $item->workerId->toUuid()->equals($previousId)) {
                $item->workerId = $newWorkerId;

                continue;
            }
        }
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getClosedBalance(): ?Money
    {
        if (null === $this->close) {
            throw new LogicException('Order not closed to get ClosedBalance.');
        }

        if (!$this->close instanceof OrderDeal) {
            throw new LogicException(sprintf('Order "%s" not dealed to get balance', $this->toId()->toString()));
        }

        return $this->close->balance;
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
            && (null === $this->carId || null !== $this->mileage);
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

    public function getTotalForPayment(Money $balance = null, bool $withPayments = true): Money
    {
        $forPayment = (new Money(0, new Currency('RUB')))
            ->add($this->getTotalPartPrice(true))
            ->add($this->getTotalServicePrice(true))
        ;

        if ($withPayments) {
            $forPayment = $forPayment->subtract($this->getTotalPayments());
        }

        if ($balance instanceof Money) {
            $forPayment = $forPayment->add($balance->multiply(-1));
        }

        return $forPayment;
    }

    public function addPayment(Money $money, ?string $description): void
    {
        $this->payments[] = new OrderPayment($this, $money, $description);
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
        $last = $this->suspends->last();
        assert($last instanceof OrderSuspend);

        return $last;
    }

    public function getSuspends(): array
    {
        return $this->suspends->getValues();
    }

    public function suspend(DateTimeImmutable $till, string $reason): void
    {
        $this->suspends[] = new OrderSuspend($this, $till, $reason);
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
            throw new LogicException(sprintf('Discount for class "%s" is null', $class));
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

            if ($item instanceof OrderItemGroup) {
                continue;
            }

            if ($item instanceof TotalPriceInterface) {
                $price = $price->add($item->getTotalPrice($withDiscount));
            } else {
                throw new DomainException('Can\'t calculate total price for item which not have price');
            }
        }

        return $price;
    }
}
