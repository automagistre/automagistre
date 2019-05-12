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

    /**
     * @return array<int, Income>
     */
    public function unpaidIncome(Operand $supplier): array
    {
        $balance = $this->paymentManager->balance($supplier);

        if (!$balance->isPositive()) {
            return [];
        }

        /** @var Income[] $incomes */
        $incomes = $this->registry->repository(Income::class)
            ->createQueryBuilder('entity')
            ->where('entity.supplier.id = :supplier')
            ->andWhere('entity.accruedAt IS NOT NULL')
            ->orderBy('entity.accruedAt', 'DESC')
            ->addOrderBy('entity.id', 'DESC')
            ->getQuery()
            ->setMaxResults(10)
            ->setParameters([
                'supplier' => $supplier->getId(),
            ])
            ->getResult();

        $result = [];
        foreach ($incomes as $income) {
            if (!$balance->isPositive()) {
                break;
            }

            $balance = $balance->subtract($income->getAccruedAmount());

            $result[(int) $income->getId()] = $income;
        }

        return $result;
    }
}
