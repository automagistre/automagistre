<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Expense
{
    use Identity;

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
     * @ORM\ManyToOne(targetEntity="Wallet")
     */
    private $wallet;

    public function __construct(string $name, Wallet $wallet = null)
    {
        $this->name = $name;
        $this->wallet = $wallet;
    }

    public function __toString(): string
    {
        return $this->getName();
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
