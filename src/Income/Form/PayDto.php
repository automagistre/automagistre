<?php

namespace App\Income\Form;

use App\Wallet\Entity\WalletId;
use Money\Money;

/**
 * @psalm-suppress MissingConstructor
 */
final class PayDto
{
    /**
     * @var Money
     */
    public $money;

    /**
     * @var WalletId
     */
    public $walletId;
}
