<?php

declare(strict_types=1);

namespace App\Wallet\View;

use App\Payment\Manager\PaymentManager;
use App\Payment\Transactional;
use Money\Money;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WalletExtension extends AbstractExtension
{
    private PaymentManager $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('balance', fn (Transactional $transactional
            ): Money => $this->paymentManager->balance($transactional)),
        ];
    }
}
