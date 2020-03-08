<?php

namespace App\Customer\Domain;

use App\Doctrine\ORM\Type\CustomId;
use App\Operand\Domain\OperandId;

interface CustomerRepository
{
    public function store(Customer $customer): void;

    public function get(CustomId $customId): Customer;

    public function getByOperandId(OperandId $operandId): Customer;
}
