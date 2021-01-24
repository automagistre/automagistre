<?php

declare(strict_types=1);

namespace App\Expense\Form;

use App\Expense\Entity\ExpenseId;
use App\Wallet\Entity\WalletId;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
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
     * @var null|string
     */
    public $description;
}
