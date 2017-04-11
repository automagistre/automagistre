<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OrderService
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="services")
     * @ORM\JoinColumn()
     */
    private $order;

    /**
     * @var Service
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Service")
     * @ORM\JoinColumn()
     */
    private $service;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $cost;

    /**
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
     * @ORM\JoinColumn()
     */
    private $worker;

    /**
     * @var OrderPart[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\OrderPart", mappedBy="orderService", cascade={"persist"}, orphanRemoval=true)
     */
    private $orderParts;

    public function __construct(Order $order = null, Service $service = null, int $price = null)
    {
        $this->order = $order;
        $this->service = $service;
        $this->cost = $price;
        $this->orderParts = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(Service $service): void
    {
        $this->service = $service;
    }

    public function getWorker(): ?Operand
    {
        return $this->worker;
    }

    public function setWorker(Operand $worker): void
    {
        $this->worker = $worker;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): void
    {
        $this->cost = $cost;
    }

    public function getPartsCost(): int
    {
        return array_sum($this->orderParts->map(function (OrderPart $part) {
            return $part->getTotalCost();
        })->toArray());
    }

    public function getOrderParts()
    {
        return $this->orderParts;
    }

    public function addOrderPart(OrderPart $orderPart): void
    {
        $this->orderParts[] = $orderPart;
    }

    public function removeOrderPart(OrderPart $orderPart): void
    {
        $this->orderParts->remove($orderPart);
    }

    public function __toString(): string
    {
        return $this->getService()->getName();
    }
}
