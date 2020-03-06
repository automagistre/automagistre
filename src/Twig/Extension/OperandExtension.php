<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\Landlord\Operand;
use App\Manager\PaymentManager;
use Money\Money;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OperandExtension extends AbstractExtension
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
            new TwigFilter('balance', fn (Operand $transactional): Money => $this->paymentManager->balance($transactional)),
        ];
    }
}
