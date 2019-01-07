<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Manager\PaymentManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class WalletExtension extends AbstractExtension
{
    /**
     * @var PaymentManager
     */
    private $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('balance', [$this->paymentManager, 'balance']),
        ];
    }
}
