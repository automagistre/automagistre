<?php

declare(strict_types=1);

namespace App\Income\Manager;

use App\Customer\Entity\CustomerView;
use App\Customer\Entity\OperandId;
use App\Doctrine\Registry;
use App\Income\Entity\Income;
use LogicException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SupplierManager
{
    public function __construct(private Registry $registry)
    {
    }

    /**
     * @return array<string, Income>
     */
    public function unpaidIncome(OperandId $supplierId): array
    {
        $balance = $this->registry->get(CustomerView::class, $supplierId)->balance;

        if (!$balance->isPositive()) {
            return [];
        }

        /** @var Income[] $incomes */
        $incomes = $this->registry->repository(Income::class)
            ->createQueryBuilder('entity')
            ->where('entity.supplierId = :supplier')
            ->join('entity.accrue', 'accrue')
            ->orderBy('accrue.id', 'DESC')
            ->getQuery()
            ->setMaxResults(10)
            ->setParameter('supplier', $supplierId)
            ->getResult()
        ;

        $result = [];
        foreach ($incomes as $income) {
            if (!$balance->isPositive()) {
                break;
            }

            $balance = $balance->subtract($income->getAccrue()?->amount ?? throw new LogicException());

            $result[$income->toId()->toString()] = $income;
        }

        return $result;
    }
}
