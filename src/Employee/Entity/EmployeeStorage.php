<?php

declare(strict_types=1);

namespace App\Employee\Entity;

use App\Customer\Entity\CustomerStorage;
use App\Customer\Entity\OperandId;
use App\Shared\Doctrine\Registry;

final class EmployeeStorage
{
    private Registry $registry;

    private CustomerStorage $customerStorage;

    public function __construct(Registry $registry, CustomerStorage $customerStorage)
    {
        $this->registry = $registry;
        $this->customerStorage = $customerStorage;
    }

    public function chargeable(OperandId $operandId): ?ChargeableEmployee
    {
        $em = $this->registry->manager();

        /** @var Employee|null $employee */
        $employee = $em
            ->createQueryBuilder()
            ->select('t')
            ->from(Employee::class, 't')
            ->where('t.personId = :personId')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->setParameter('personId', $operandId)
            ->getOneOrNullResult();

        if (null === $employee) {
            return null;
        }

        $rate = $employee->getRatio();
        if (null === $rate) {
            return null;
        }

        return new ChargeableEmployee(
            $this->customerStorage->getTransactional($operandId),
            $rate,
        );
    }
}
