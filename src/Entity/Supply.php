<?php

declare(strict_types=1);

namespace App\Entity;

use App\Uuid\UuidGenerator;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 */
class Supply
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
     * @ORM\JoinColumn(nullable=false)
     */
    private $supplier;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Part")
     * @ORM\JoinColumn(nullable=false)
     */
    private $part;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $receivedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $receivedAt;

    public function __construct(Operand $supplier, Part $part, int $quantity, User $createdBy)
    {
        $this->id = UuidGenerator::generate();
        $this->supplier = $supplier;
        $this->part = $part;
        $this->quantity = $quantity;
        $this->createdBy = $createdBy;
        $this->createdAt = new \DateTime();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getSupplier(): Operand
    {
        return $this->supplier;
    }

    public function getPart(): Part
    {
        return $this->part;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): \DateTime
    {
        return clone $this->createdAt;
    }

    public function getReceivedBy(): ?User
    {
        return $this->receivedBy;
    }

    public function getReceivedAt(): ?\DateTime
    {
        return $this->receivedAt ? clone $this->receivedAt : null;
    }

    public function receive(User $user)
    {
        $this->receivedBy = $user;
        $this->receivedAt = new \DateTime();
    }
}
