<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Enum\OrderStatus;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @var JobItem[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\JobItem", mappedBy="order")
     */
    private $jobs;

    /**
     * @var PartItem[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PartItem", mappedBy="order")
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Car")
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
     * @var Mileage
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Mileage")
     * @ORM\JoinColumn(nullable=true)
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
        $this->status = OrderStatus::draft();
        $this->jobs = new ArrayCollection();
        $this->parts = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return JobItem[]|ArrayCollection
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @param JobItem[]|ArrayCollection $jobs
     */
    public function setJobs($jobs)
    {
        $this->jobs = $jobs;
    }

    /**
     * @return PartItem[]|ArrayCollection
     */
    public function getParts()
    {
        return $this->parts;
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
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @param PartItem[]|ArrayCollection $parts
     */
    public function addParts(PartItem $parts)
    {
        $this->parts[] = $parts;
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

    public function getStatus(): OrderStatus
    {
        if (!$this->status instanceof OrderStatus) {
            $this->status = new OrderStatus($this->status);
        }

        return $this->status;
    }

    public function jobsCost(): int
    {
        $total = 0;
        foreach ($this->jobs as $job) {
            $total += $job->getCost();
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
        return sprintf('%d / %d', $this->jobsCost(), $this->partsCost());
    }
}
