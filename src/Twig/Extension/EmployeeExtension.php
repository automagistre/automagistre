<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\Employee;
use App\Manager\PaymentManager;
use Money\Money;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class EmployeeExtension extends AbstractExtension
{
    /**
     * @var PaymentManager
     */
    private $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('employee_balance', [$this, 'balance']),
        ];
    }

    public function balance(Employee $employee): Money
    {
        return $this->paymentManager->balance($employee->getPerson());
    }
}
