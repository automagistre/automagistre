<?php

declare(strict_types=1);

namespace App\Expense\Form;

use App\Wallet\Entity\WalletId;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
class ExpenseDto
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    public $name;

    /**
     * @var WalletId|null
     */
    public $walletId;
}
