<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use App\Shared\Doctrine\Registry;

final class CustomerStorage
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function get(OperandId $operandId): Operand
    {
        return $this->registry->get(Operand::class, $operandId);
    }

    public function getTransactional(OperandId $operandId): Transactional
    {
        return new TransactionalCustomer($operandId, $this->registry->manager());
    }

    public function view(OperandId $operandId): CustomerView
    {
        return $this->registry->get(CustomerView::class, $operandId);
    }
}
