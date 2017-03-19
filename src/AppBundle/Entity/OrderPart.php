<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OrderPart
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
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Order", inversedBy="parts")
     * @ORM\JoinColumn()
     */
    private $order;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Part")
     * @ORM\JoinColumn()
     */
    private $part;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $cost;

    /**
     * @var OrderService
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OrderService", inversedBy="orderParts")
     * @ORM\JoinColumn()
     */
    private $orderService;

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        if ($this->order) {
            throw new \DomainException('Changing order is restricted');
        }

        $this->order = $order;
    }

    public function getPart(): ?Part
    {
        return $this->part;
    }

    public function setPart(Part $part): void
    {
        $this->part = $part;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): void
    {
        $this->cost = $cost;
    }

    public function getTotalCost(): int
    {
        return $this->getCost() * $this->getQuantity();
    }

    public function getOrderService(): OrderService
    {
        return $this->orderService;
    }

    public function setOrderService(OrderService $orderService): void
    {
        $this->orderService = $orderService;
    }
}
