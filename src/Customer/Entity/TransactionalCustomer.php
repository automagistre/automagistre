<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Customer\Enum\CustomerTransactionSource;
use Doctrine\ORM\EntityManagerInterface;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

final class TransactionalCustomer implements Transactional
{
    private OperandId $operandId;

    private EntityManagerInterface $em;

    public function __construct(OperandId $operandId, EntityManagerInterface $em)
    {
        $this->operandId = $operandId;
        $this->em = $em;
    }

    public function addTransaction(
        Money $money,
        CustomerTransactionSource $source,
        UuidInterface $sourceId,
        string $description = null
    ): void {
        $this->em->persist(
            new CustomerTransaction(
                CustomerTransactionId::generate(),
                $this->operandId,
                $money,
                $source,
                $sourceId,
                $description,
            )
        );
    }
}
