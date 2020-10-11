<?php

declare(strict_types=1);

namespace App\Employee\Entity;

use App\Customer\Entity\Transactional;
use App\Customer\Enum\CustomerTransactionSource;
use App\Order\Entity\OrderId;
use Money\Money;

final class ChargeableEmployee
{
    private Transactional $employee;

    private int $rate;

    public function __construct(Transactional $employee, int $rate)
    {
        $this->employee = $employee;
        $this->rate = $rate;
    }

    public function chargeByOrder(OrderId $orderId, Money $profit): void
    {
        $salary = $profit->multiply($this->rate / 100);

        $this->employee->addTransaction(
            $salary->absolute(),
            CustomerTransactionSource::orderSalary(),
            $orderId->toUuid(),
        );
    }
}
