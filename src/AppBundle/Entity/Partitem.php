<?php

namespace AppBundle\Entity;

use AppBundle\Doctrine\PropertyAccessorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Partitem
 *
 * @ORM\Table(name="partitem", indexes={@ORM\Index(name="_order_id", columns={"_order_id"})})
 * @ORM\Entity
 */
class Partitem
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
     * @ORM\Column(name="jobitem_id", type="integer", nullable=true)
     */
    private $jobitemId;

    /**
     * @var integer
     *
     * @ORM\Column(name="part_id", type="integer", nullable=true)
     */
    private $partId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_order", type="boolean", nullable=true)
     */
    private $isOrder;

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
     * @var string
     *
     * @ORM\Column(name="qty", type="decimal", precision=5, scale=1, nullable=false)
     */
    private $qty = '0.0';

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Order", inversedBy="parts")
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
     * @var integer
     *
     * @ORM\Column(name="motion_id", type="integer", nullable=true)
     */
    private $motionId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="move_motion_id", type="boolean", nullable=true)
     */
    private $moveMotionId;

    /**
     * @return int
     */
    public function getCost(): int
    {
        return $this->cost;
    }
}

