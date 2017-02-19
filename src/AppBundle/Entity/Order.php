<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Order
 *
 * @ORM\Table(name="_order", indexes={@ORM\Index(name="IDX_ORDER_CLIENT", columns={"client_id"}), @ORM\Index(name="IDX_ORDER_CAR", columns={"car_id"})})
 * @ORM\Entity
 */
class Order
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
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
     * @var integer
     *
     * @ORM\Column(name="car_id", type="integer", nullable=true)
     */
    private $carId;

    /**
     * @var integer
     *
     * @ORM\Column(name="client_id", type="integer", nullable=true)
     */
    private $clientId;

    /**
     * @var integer
     *
     * @ORM\Column(name="mileage_id", type="integer", nullable=true)
     */
    private $mileageId;

    /**
     * @var boolean
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
     * @var integer
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
     * @var boolean
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
     * @var boolean
     *
     * @ORM\Column(name="paypoints", type="boolean", nullable=true)
     */
    private $paypoints;

    /**
     * @var integer
     *
     * @ORM\Column(name="bonus", type="integer", nullable=true)
     */
    private $bonus;

    /**
     * @var boolean
     *
     * @ORM\Column(name="points", type="boolean", nullable=true)
     */
    private $points;

    /**
     * @var boolean
     *
     * @ORM\Column(name="paycardbool", type="boolean", nullable=true)
     */
    private $paycardbool;

    /**
     * @var integer
     *
     * @ORM\Column(name="paycard", type="integer", nullable=true)
     */
    private $paycard;

}

