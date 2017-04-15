<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Motion
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
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Part")
     * @ORM\JoinColumn()
     */
    private $part;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Order")
     */
    private $order;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $description;

    public function __construct(int $qty = null, Part $part = null, Order $order = null)
    {
        $this->quantity = $qty;
        $this->part = $part;
        $this->order = $order;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPart(): ?Part
    {
        return $this->part;
    }

    public function setPart(Part $part): void
    {
        $this->part = $part;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
