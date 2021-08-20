<?php

declare(strict_types=1);

namespace App\Employee\Entity;

use App\Customer\Entity\OperandId;
use App\Customer\Entity\TransactionalCustomer;
use App\Doctrine\Registry;

final class EmployeeStorage
{
    public function __construct(private Registry $registry)
    {
    }

    public function chargeable(OperandId $operandId): ?ChargeableEmployee
    {
        $em = $this->registry->manager();

        /** @var null|Employee $employee */
        $employee = $em
            ->createQueryBuilder()
            ->select('t')
            ->from(Employee::class, 't')
            ->where('t.personId = :personId')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->setParameter('personId', $operandId)
            ->getOneOrNullResult()
        ;

        if (null === $employee) {
            return null;
        }

        $rate = $employee->getRatio();

        if (null === $rate) {
            return null;
        }

        return new ChargeableEmployee(
            new TransactionalCustomer($operandId, $this->registry->manager()),
            $rate,
        );
    }
}
