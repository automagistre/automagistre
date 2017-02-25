<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PartItem
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
     * @var JobItem
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JobItem")
     * @ORM\JoinColumn()
     */
    private $jobItem;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Part")
     * @ORM\JoinColumn()
     */
    private $part;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_order", type="boolean", nullable=true)
     */
    private $isOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="name", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="cost", nullable=true)
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
     * @ORM\JoinColumn()
     */
    private $order;

    /**
     * @var JobAdvice
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JobAdvice")
     * @ORM\JoinColumn()
     */
    private $jobAdvice;

    /**
     * @var int
     *
     * @ORM\Column(name="motion_id", type="integer", nullable=true)
     */
    private $motionId;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Part
     */
    public function getPart(): ?Part
    {
        return $this->part;
    }

    /**
     * @param Part $part
     */
    public function setPart(Part $part)
    {
        $this->part = $part;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getQty(): ?string
    {
        return $this->qty;
    }

    /**
     * @param string $qty
     */
    public function setQty(string $qty)
    {
        $this->qty = $qty;
    }

    /**
     * @return Order
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getCost(): string
    {
        return $this->cost;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->getName(), $this->getCost());
    }
}
