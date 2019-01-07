<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class OperandTransaction extends Transaction
{
    /**
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
     * @ORM\JoinColumn
     */
    private $recipient;

    public function __construct(Operand $operand, string $description, Money $money, Money $subtotal)
    {
        $this->recipient = $operand;

        parent::__construct($description, $money, $subtotal);
    }

    public function getRecipient(): Operand
    {
        return $this->recipient;
    }
}
