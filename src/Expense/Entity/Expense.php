<?php

declare(strict_types=1);

namespace App\Expense\Entity;

use App\Wallet\Entity\WalletId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Expense
{
    /**
     * @ORM\Id
     * @ORM\Column(type="expense_id")
     */
    public ExpenseId $id;

    /**
     * @var string
     *
     * @ORM\Column
     */
    public $name;

    /**
     * Счет списания по умолчанию.
     *
     * @ORM\Column(type="wallet_id", nullable=true)
     */
    public ?WalletId $walletId;

    public function __construct(string $name, WalletId $walletId = null)
    {
        $this->id = ExpenseId::generate();
        $this->name = $name;
        $this->walletId = $walletId;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function toId(): ExpenseId
    {
        return $this->id;
    }
}
