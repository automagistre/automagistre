<?php

declare(strict_types=1);

namespace App\Expense\Entity;

use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use App\Wallet\Entity\Wallet;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class Expense
{
    use Identity;

    /**
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $uuid;

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
        $this->uuid = Uuid::uuid6();
        $this->name = $name;
        $this->wallet = $wallet;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function toId(): ExpenseId
    {
        return ExpenseId::fromUuid($this->uuid);
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
