<?php

namespace App\Customer\Infrastructure;

use App\Customer\Domain\Customer;
use App\Customer\Domain\CustomerRepository;
use App\Doctrine\ORM\Type\CustomId;
use App\Doctrine\Registry;
use App\Operand\Domain\OperandId;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineCustomerRepository implements CustomerRepository
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function store(Customer $customer): void
    {
        $this->em()->persist($customer);
    }

    public function get(CustomId $customId): Customer
    {
        return $this->em()->getRepository(Customer::class)->find($customId);
    }

    public function getByOperandId(OperandId $operandId): Customer
    {
        $em = $this->em();

        $customer = $em->getRepository(Customer::class)->findOneBy(['operand' => $operandId]);
        if (null === $customer) {
            $customer = Customer::createByOperandId($operandId);
            $em->persist($em);
        }

        return $customer;
    }

    private function em(): EntityManagerInterface
    {
        return $this->registry->manager(Customer::class);
    }
}
