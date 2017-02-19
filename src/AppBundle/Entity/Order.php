<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order.
 *
 * @ORM\Table(name="_order", indexes={@ORM\Index(name="IDX_ORDER_CLIENT", columns={"client_id"}), @ORM\Index(name="IDX_ORDER_CAR", columns={"car_id"})})
 * @ORM\Entity
 */
class Order
{
    use PropertyAccessorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Jobitem[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Jobitem", mappedBy="order")
     */
    private $jobs;

    /**
     * @var Partitem[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Partitem", mappedBy="order")
     */
    private $parts;

    /**
     * @var int
     *
     * @ORM\Column(name="ownedsecurableitem_id", type="integer", nullable=true)
     */
    private $ownedsecurableitemId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startdate", type="datetime", nullable=true)
     */
    private $startdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="closeddate", type="datetime", nullable=true)
     */
    private $closeddate;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="refs", type="string", length=255, nullable=true)
     */
    private $refs;

    /**
     * @var Car
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Car")
     * @ORM\JoinColumn()
     */
    private $car;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumn()
     */
    private $client;

    /**
     * @var int
     *
     * @ORM\Column(name="mileage_id", type="integer", nullable=true)
     */
    private $mileageId;

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
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="eid", type="integer", nullable=true)
     */
    private $eid;

    /**
     * @var \DateTime
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
     * @var \DateTime
     *
     * @ORM\Column(name="resumedate", type="date", nullable=true)
     */
    private $resumedate;

    /**
     * @var bool
     *
     * @ORM\Column(name="paypoints", type="boolean", nullable=true)
     */
    private $paypoints;

    /**
     * @var int
     *
     * @ORM\Column(name="bonus", type="integer", nullable=true)
     */
    private $bonus;

    /**
     * @var bool
     *
     * @ORM\Column(name="points", type="boolean", nullable=true)
     */
    private $points;

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
        $this->jobs = new ArrayCollection();
        $this->parts = new ArrayCollection();
    }

    public function getStatus(): ?string
    {
        return 0 === strpos($this->status, 'swOrder/') ? substr($this->status, 8) : $this->status;
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
        return sprintf('%s / %s', $this->jobsCost(), $this->partsCost());
    }
}
