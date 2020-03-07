<?php

declare(strict_types=1);

namespace App\Wallet;

use App\Doctrine\Registry;
use App\Entity\Tenant\Wallet;
use App\Entity\Tenant\WalletTransaction;
use function array_shift;
use Money\Currency;
use Money\Money;

final class BalanceProvider
{
    /** @var Registry */
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function balance(Wallet $wallet): Money
    {
        $result = $this->registry->manager(WalletTransaction::class)
            ->createQueryBuilder()
            ->select('SUM(CAST(payment.amount.amount AS integer)) as amount, payment.amount.currency.code as code')
            ->from(WalletTransaction::class, 'payment')
            ->where('payment.recipient = :recipient')
            ->setParameter('recipient', $wallet)
            ->getQuery()
            ->getResult();

        $result = array_shift($result);

        return new Money($result['amount'], new Currency($result['code']));
    }
}
