<?php

namespace App\Customer\Domain\Event;

use App\Doctrine\ORM\Type\Identifier;
use App\Operand\Domain\OperandId;
use Symfony\Contracts\EventDispatcher\Event;

final class CustomerLinkedWithOperand extends Event
{
    /**
     * @var Identifier
     */
    private Identifier $customId;

    /**
     * @var OperandId
     */
    private OperandId $operandId;

    public function __construct(Identifier $customId, OperandId $operandId)
    {
        $this->customId = $customId;
        $this->operandId = $operandId;
    }

    public function customId(): Identifier
    {
        return $this->customId;
    }

    public function operandId(): OperandId
    {
        return $this->operandId;
    }
}
