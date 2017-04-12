<?php

namespace App\Entity;

use App\Entity\Enum\OrderStatus;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity
 */
class Order
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var OrderService[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\OrderService", mappedBy="order", cascade={"persist"}, orphanRemoval=true)
     */
    private $services;

    /**
     * @var OrderPart[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\OrderPart", mappedBy="order", cascade={"persist"}, orphanRemoval=true)
     */
    private $parts;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="startdate", type="datetime", nullable=true)
     */
    private $startdate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="closeddate", type="datetime", nullable=true)
     */
    private $closeddate;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
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
     * @ORM\JoinColumn()
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", length=8, nullable=true, options={"unsigned"=true})
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

    public function __construct()
    {
        $this->status = OrderStatus::DRAFT;
        $this->services = new ArrayCollection();
        $this->parts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServices(): array
    {
        return $this->services->toArray();
    }

    public function addService(OrderService $service)
    {
        $service->setOrder($this);
        $this->services[] = $service;
    }

    public function getParts(): array
    {
        return $this->parts->toArray();
    }

    public function addPart(OrderPart $part)
    {
        $part->setOrder($this);
        $this->parts[] = $part;
    }

    public function getRootParts(): array
    {
        $criteria = Criteria::create()->where(Criteria::expr()->isNull('orderService'));

        return $this->parts->matching($criteria)->toArray();
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(Car $car)
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

    public function setDescription(string $description = null)
    {
        $this->description = $description;
    }

    public function getNotes()
    {
        return $this->notes->toArray();
    }

    public function getStartedAt(): ?DateTime
    {
        return $this->startdate;
    }

    public function getClosedAt(): ?DateTime
    {
        return $this->closeddate;
    }

    public function getMileage(): ?string
    {
        return $this->mileage;
    }

    public function setMileage(string $mileage): void
    {
        $this->mileage = $mileage;
    }

    public function getStatus(): OrderStatus
    {
        return new OrderStatus($this->status);
    }

    public function servicesCost(): int
    {
        return array_sum(array_map(function (OrderService $service) {
            return $service->getCost();
        }, $this->services->toArray()));
    }

    public function partsCost(): int
    {
        return array_sum(array_map(function (OrderPart $part) {
            return $part->getCost();
        }, $this->parts->toArray()));
    }

    public function readableCosts(): string
    {
        return sprintf('%d / %d', $this->servicesCost(), $this->partsCost());
    }

    public function isEditable(): bool
    {
        return $this->getStatus()->isEditable();
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }

    public function realizeRecommendation(CarRecommendation $recommendation): void
    {
        $orderService = new OrderService($this, $recommendation->getService(), $recommendation->getCost());
        $this->services[] = $orderService;

        foreach ($recommendation->getParts() as $recommendationPart) {
            $orderPart = new OrderPart(
                $this,
                $recommendationPart->getPart(),
                $recommendationPart->getQuantity(),
                $recommendationPart->getCost(),
                $orderService
            );

            $this->parts[] = $orderPart;
            $orderService->addOrderPart($orderPart);
        }

        $recommendation->realize($this);
    }

    public function recommendService(OrderService $orderService): void
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('realization', $this))
            ->andWhere(Criteria::expr()->eq('service', $orderService->getService()));

        $recommendations = $this->car->getRecommendations($criteria);
        if ($recommendations) {
            $recommendation = array_shift($recommendations);

            $recommendation->unRealize($this);
            $recommendation->setCost($orderService->getCost());
        } else {
            $recommendation = new CarRecommendation($this->car, $orderService->getService(), $orderService->getCost());
        }

        foreach ($orderService->getOrderParts() as $orderPart) {
            if ($this->parts->contains($orderPart)) {
                $this->parts->removeElement($orderPart);
            }

            $recommendation->addPart(new CarRecommendationPart(
                $recommendation,
                $orderPart->getPart(),
                $orderPart->getQuantity(),
                $orderPart->getCost()
            ));
        }

        $this->services->removeElement($orderService);
        $this->car->addRecommendation($recommendation);
    }

    public function linkOrderToParts(): void
    {
        $criteria = Criteria::create()->where(Criteria::expr()->isNull('order'));

        $this->parts->matching($criteria)->map(function (OrderPart $part) {
            $part->setOrder($this);
        });
    }

    public function linkOrderToServices(): void
    {
        $criteria = Criteria::create()->where(Criteria::expr()->isNull('order'));

        $this->services->matching($criteria)->map(function (OrderService $service) {
            $service->setOrder($this);

            $criteria = Criteria::create()->where(Criteria::expr()->isNull('orderService'));
            $service->getOrderParts()->matching($criteria)->map(function (OrderPart $orderPart) use ($service) {
                $orderPart->setOrderService($service);
            });
        });
    }
}
