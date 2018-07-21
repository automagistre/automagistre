<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Enum\OrderStatus;
use App\Money\PriceInterface;
use App\Money\TotalPriceInterface;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
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

    /**
     * @var OrderItem[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", mappedBy="order", cascade={"persist"}, orphanRemoval=true)
     */
    private $items;

    /**
     * @var DateTimeImmutable|null
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $closedAt;

    /**
     * @var OrderStatus
     *
     * @ORM\Column(name="status", type="order_status_enum")
     */
    private $status;

    /**
     * @var Car
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Car", inversedBy="orders")
     * @ORM\JoinColumn(nullable=true)
     */
    private $car;

    /**
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
     * @ORM\JoinColumn
     */
    private $customer;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=8, nullable=true, options={"unsigned": true})
     */
    private $mileage;

    /**
     * @var bool
     *
     * @ORM\Column(name="checkpay", type="boolean", nullable=true)
     */
    private $checkpay;

    /**
     * @var float
     *
     * @ORM\Column(name="topay", type="float", precision=10, scale=0, nullable=true)
     */
    private $topay;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var Note[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Note", mappedBy="order")
     */
    private $notes;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="suspenddate", type="datetime", nullable=true)
     */
    private $suspenddate;

    /**
     * @var bool
     *
     * @ORM\Column(name="suspended", type="boolean", nullable=true)
     */
    private $suspended;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="resumedate", type="date", nullable=true)
     */
    private $resumedate;

    /**
     * @var bool
     *
     * @ORM\Column(name="paycardbool", type="boolean", nullable=true)
     */
    private $paycardbool;

    /**
     * @var int
     *
     * @ORM\Column(name="paycard", type="integer", nullable=true)
     */
    private $paycard;

    /**
     * @var OrderPayment[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\OrderPayment", mappedBy="order")
     */
    private $payments;

    public function __construct()
    {
        $this->status = OrderStatus::draft();
        $this->items = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getId();
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

    public function close(): void
    {
        $this->status = OrderStatus::closed();
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
        return $this->car;
    }

    public function setCar(Car $car): void
    {
        $this->car = $car;
    }

    public function getCustomer(): ?Operand
    {
        return $this->customer;
    }

    public function setCustomer(Operand $customer = null): void
    {
        $this->customer = $customer;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description = null): void
    {
        $this->description = $description;
    }

    public function getNotes(): array
    {
        return $this->notes->toArray();
    }

    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(int $mileage): void
    {
        $this->mileage = $mileage;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
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

    public function getTotalPayments(): Money
    {
        $money = new Money(0, new Currency('RUB'));
        foreach ($this->payments as $payment) {
            $money = $money->add($payment->getPayment()->getAmount());
        }

        return $money;
    }

    public function getTotalForPayment(): Money
    {
        return (new Money(0, new Currency('RUB')))
            ->add($this->getTotalPartPrice())
            ->add($this->getTotalServicePrice())
            ->subtract($this->getTotalPayments());
    }

    /**
     * @return OrderPayment[]
     */
    public function getPayments(): array
    {
        return $this->payments->toArray();
    }

    private function getTotalPriceByClass(string $class): Money
    {
        $price = new Money(0, new Currency('RUB'));

        foreach ($this->getItems($class) as $item) {
            if ($item instanceof TotalPriceInterface) {
                $price = $price->add($item->getTotalPrice());
            } elseif ($item instanceof PriceInterface) {
                $price = $price->add($item->getPrice());
            } else {
                throw new DomainException('Can\'t calculate total price for item which not have price');
            }
        }

        return $price;
    }
}
