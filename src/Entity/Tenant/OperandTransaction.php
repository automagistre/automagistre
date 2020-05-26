<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Customer\Entity\Operand;
use App\Entity\Embeddable\OperandRelation;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class OperandTransaction extends Transaction
{
    /**
     * @ORM\Embedded(class=OperandRelation::class)
     */
    private OperandRelation $recipient;

    public function __construct(Operand $operand, string $description, Money $money)
    {
        $this->recipient = new OperandRelation($operand);

        parent::__construct($description, $money);
    }

    public function getRecipient(): Operand
    {
        return $this->recipient->entity();
    }
}
