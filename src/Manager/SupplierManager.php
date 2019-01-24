<?php

declare(strict_types=1);

namespace App\Manager;

use App\Doctrine\Registry;
use App\Entity\Landlord\Operand;
use App\Entity\Tenant\Income;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SupplierManager
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var PaymentManager
     */
    private $paymentManager;

    public function __construct(Registry $registry, PaymentManager $paymentManager)
    {
        $this->registry = $registry;
        $this->paymentManager = $paymentManager;
    }

    public function unpaidIncome(Operand $supplier): array
    {
        $balance = $this->paymentManager->balance($supplier);

        if (!$balance->isPositive()) {
            return [];
        }

        /** @var Income[] $incomes */
        $incomes = $this->registry->repository(Income::class)->findBy(
            ['supplier.id' => $supplier->getId()],
            ['accruedAt' => 'DESC', 'id' => 'DESC'],
            10
        );

        $result = [];
        foreach ($incomes as $income) {
            if (!$balance->isPositive()) {
                break;
            }

            $balance = $balance->subtract($income->getAccruedAmount());

            $result[$income->getId()] = $income;
        }

        return $result;
    }
}
