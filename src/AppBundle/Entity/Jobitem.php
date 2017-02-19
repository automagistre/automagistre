<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jobitem
 *
 * @ORM\Table(name="jobitem", indexes={@ORM\Index(name="_order_id", columns={"_order_id"})})
 * @ORM\Entity
 */
class Jobitem
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
     * @ORM\Column(name="_user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="cost", type="string", length=255, nullable=true)
     */
    private $cost;

    /**
     * @var integer
     *
     * @ORM\Column(name="_order_id", type="integer", nullable=true)
     */
    private $orderId;

    /**
     * @var integer
     *
     * @ORM\Column(name="jobadvice_id", type="integer", nullable=true)
     */
    private $jobadviceId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="employee__user_id", type="boolean", nullable=true)
     */
    private $employeeUserId;

}

