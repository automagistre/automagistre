<?php

declare(strict_types=1);

namespace App\Expense\Form;

use App\Expense\Entity\ExpenseId;
use App\Wallet\Entity\WalletId;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

final class ExpenseItemDto
{
    /**
     * @var ExpenseId
     *
     * @Assert\NotBlank
     */
    public $expenseId;

    /**
     * @var WalletId
     *
     * @Assert\NotBlank
     */
    public $walletId;

    /**
     * @var Money
     *
     * @Assert\NotBlank
     */
    public $money;

    /**
     * @var string|null
     */
    public $description;

    public function __construct(ExpenseId $expenseId, WalletId $walletId, Money $money, ?string $description)
    {
        $this->expenseId = $expenseId;
        $this->walletId = $walletId;
        $this->money = $money;
        $this->description = $description;
    }
}
