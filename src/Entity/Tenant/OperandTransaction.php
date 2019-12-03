<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Entity\Embeddable\OperandRelation;
use App\Entity\Landlord\Operand;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class OperandTransaction extends Transaction
{
    /**
     * @var OperandRelation
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\OperandRelation")
     */
    private $recipient;

    public function __construct(Operand $operand, string $description, Money $money, Money $subtotal, User $user)
    {
        $this->recipient = new OperandRelation($operand);

        parent::__construct($description, $money, $subtotal, $user);
    }

    public function getRecipient(): Operand
    {
        return $this->recipient->entity();
    }
}
