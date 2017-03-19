<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Enum\OrderStatus;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @property CustomerInterface $customer
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="customer_type", type="integer")
 * @ORM\DiscriminatorMap({"1" = "\AppBundle\Entity\OrderPerson", "2" = "\AppBundle\Entity\OrderOrganization"})
 */
abstract class Order
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
     * @var Service[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\OrderService", mappedBy="order", cascade={"persist"})
     */
    private $services;

    /**
     * @var OrderPart[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\OrderPart", mappedBy="order", cascade={"persist"})
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Car", inversedBy="orders")
     * @ORM\JoinColumn(nullable=true)
     */
    private $car;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumn(nullable=true)
     */
    private $client;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Note", mappedBy="order")
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

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCustomer(CustomerInterface $customer): void
    {
        $this->{'customer'} = $customer;
    }

    public function getCustomer(): ?CustomerInterface
    {
        return $this->{'customer'};
    }

    /**
     * @return OrderService[]|ArrayCollection
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param OrderService $service
     */
    public function addService(OrderService $service)
    {
        $service->setOrder($this);
        $this->services[] = $service;
    }

    /**
     * @return OrderPart[]|ArrayCollection
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param OrderPart $part
     */
    public function addPart(OrderPart $part)
    {
        $part->setOrder($this);
        $this->parts[] = $part;
    }

    /**
     * @return OrderPart[]|ArrayCollection
     */
    public function getRootParts()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->isNull('orderService'));

        return $this->parts->matching($criteria);
    }

    /**
     * @return Car
     */
    public function getCar(): ?Car
    {
        return $this->car;
    }

    /**
     * @param Car $car
     */
    public function setCar(Car $car)
    {
        $this->car = $car;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description = null)
    {
        $this->description = $description;
    }

    /**
     * @return Note[]|ArrayCollection
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return DateTime
     */
    public function getStartedAt(): ?DateTime
    {
        return $this->startdate;
    }

    /**
     * @return DateTime
     */
    public function getClosedAt(): ?DateTime
    {
        return $this->closeddate;
    }

    /**
     * @return Client
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
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
        $total = 0;
        foreach ($this->services as $service) {
            $total += $service->getCost();
        }

        return $total;
    }

    public function partsCost(): int
    {
        $cost = 0;
        foreach ($this->parts as $part) {
            $cost += $part->getCost();
        }

        return $cost;
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

    public function linkOrderToParts(): void
    {
        $criteria = Criteria::create()->where(Criteria::expr()->isNull('order'));

        $this->getParts()->matching($criteria)->map(function (OrderPart $part) {
            $part->setOrder($this);
        });
    }

    public function linkOrderToServices(): void
    {
        $criteria = Criteria::create()->where(Criteria::expr()->isNull('order'));

        $this->getServices()->matching($criteria)->map(function (OrderService $service) {
            $service->setOrder($this);

            $criteria = Criteria::create()->where(Criteria::expr()->isNull('orderService'));
            $service->getOrderParts()->matching($criteria)->map(function (OrderPart $orderPart) use ($service) {
                $orderPart->setOrderService($service);
            });
        });
    }
}
