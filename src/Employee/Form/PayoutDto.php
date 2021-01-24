<?php

declare(strict_types=1);

namespace App\Employee\Form;

use App\Customer\Entity\OperandId;
use App\Wallet\Entity\WalletId;
use Money\Money;

/**
 * @psalm-suppress MissingConstructor
 */
final class PayoutDto
{
    /**
     * @var OperandId
     */
    public $recipient;

    /**
     * @var WalletId
     */
    public $walletId;

    /**
     * @var Money
     */
    public $amount;

    public ?string $description = null;
}
