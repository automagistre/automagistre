<?php

namespace App\Customer\Domain\Event;

use App\Doctrine\ORM\Type\CustomId;
use App\Operand\Domain\OperandId;
use Symfony\Contracts\EventDispatcher\Event;

final class CustomerLinkedWithOperand extends Event
{
    /**
     * @var CustomId
     */
    private CustomId $customId;

    /**
     * @var OperandId
     */
    private OperandId $operandId;

    public function __construct(CustomId $customId, OperandId $operandId)
    {
        $this->customId = $customId;
        $this->operandId = $operandId;
    }

    public function customId(): CustomId
    {
        return $this->customId;
    }

    public function operandId(): OperandId
    {
        return $this->operandId;
    }
}
