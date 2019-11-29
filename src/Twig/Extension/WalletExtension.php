<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\Transactional;
use App\Manager\PaymentManager;
use Money\Money;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WalletExtension extends AbstractExtension
{
    /**
     * @var PaymentManager
     */
    private $paymentManager;

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
            new TwigFilter('balance', function (Transactional $transactional): Money {
                return $this->paymentManager->balance($transactional);
            }),
        ];
    }
}
