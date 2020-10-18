<?php

declare(strict_types=1);

namespace App\Order\Form\Payment;

use App\Wallet\Entity\WalletId;
use Money\Money;

final class OrderPaymentWalletDto
{
    /**
     * @var WalletId
     */
    public $walletId;

    /**
     * @var Money
     */
    public $payment;

    public function __construct(WalletId $walletId, Money $payment)
    {
        $this->walletId = $walletId;
        $this->payment = $payment;
    }
}
