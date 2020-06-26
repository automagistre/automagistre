<?php

declare(strict_types=1);

namespace App\Expense\Entity;

use App\Wallet\Entity\Wallet;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class Expense
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $name;

    /**
     * Счет списания по умолчанию.
     *
     * @var Wallet|null
     *
     * @ORM\ManyToOne(targetEntity=Wallet::class)
     */
    private $wallet;

    public function __construct(string $name, Wallet $wallet = null)
    {
        $this->id = Uuid::uuid6();
        $this->name = $name;
        $this->wallet = $wallet;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function toId(): ExpenseId
    {
        return ExpenseId::fromUuid($this->id);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setWallet(?Wallet $wallet): void
    {
        $this->wallet = $wallet;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }
}
