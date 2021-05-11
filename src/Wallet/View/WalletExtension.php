<?php

declare(strict_types=1);

namespace App\Wallet\View;

use App\Payment\Manager\PaymentManager;
use Money\Money;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WalletExtension extends AbstractExtension
{
    public function __construct(private PaymentManager $paymentManager)
    {
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('balance', fn (
                object $transactional,
            ): Money => $this->paymentManager->balance($transactional)),
        ];
    }
}
