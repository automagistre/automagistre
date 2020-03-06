<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Entity\Embeddable\OperandRelation;
use App\Entity\Landlord\Operand;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class OperandTransaction extends Transaction
{
    /**
     * @ORM\Embedded(class="App\Entity\Embeddable\OperandRelation")
     */
    private OperandRelation $recipient;

    public function __construct(Operand $operand, string $description, Money $money, Money $subtotal)
    {
        $this->recipient = new OperandRelation($operand);

        parent::__construct($description, $money, $subtotal);
    }

    public function getRecipient(): Operand
    {
        return $this->recipient->entity();
    }
}
