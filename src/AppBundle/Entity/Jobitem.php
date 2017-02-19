<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Jobitem
 *
 * @ORM\Table(name="jobitem", indexes={@ORM\Index(name="_order_id", columns={"_order_id"})})
 * @ORM\Entity
 */
class Jobitem
{
    use PropertyAccessorTrait;

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
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Order", inversedBy="jobs")
     * @ORM\JoinColumn(name="_order_id")
     */
    private $order;

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

    /**
     * @return int
     */
    public function getCost(): int
    {
        return $this->cost;
    }
}

