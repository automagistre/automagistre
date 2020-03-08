<?php

namespace App\Customer\Domain;

use App\Operand\Domain\OperandId;

interface CustomerRepository
{
    public function store(Customer $customer): void;

    public function get(CustomerId $customerId): Customer;

    public function getByOperandId(OperandId $operandId): Customer;
}
