<?php

namespace App\Customer\Domain;

use App\Customer\Domain\Event\CustomerCreated;
use App\Customer\Domain\Event\CustomerLinkedWithOperand;
use App\Doctrine\ORM\Type\CustomId;
use App\Infrastructure\DomainEvents\RaiseEventsInterface;
use App\Infrastructure\DomainEvents\RaiseEventsTrait;
use App\Operand\Domain\OperandId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Customer implements RaiseEventsInterface
{
    use RaiseEventsTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="customer_id")
     */
    private CustomerId $id;

    /**
     * @ORM\Column(type="operand_id")
     */
    private ?OperandId $operand;

    private function __construct(CustomerId $id)
    {
        $this->id = $id;

        $this->raise(new CustomerCreated($this->id));
    }

    public static function createByOperandId(OperandId $operandId): self
    {
        $customer = new self(CustomerId::generate());
        $customer->operand = $operandId;

        return $customer;
    }

    public function id(): CustomId
    {
        return $this->id;
    }

    public function linkWithOperand(OperandId $operandId): void
    {
        if (null !== $this->operand) {
            throw new \DomainException('Operand already defined.');
        }

        $this->operand = $operandId;

        $this->raise(new CustomerLinkedWithOperand($this->id, $this->operand));
    }
}
