<?php

declare(strict_types=1);

namespace App\Customer\Form;

use App\Customer\Entity\Operand;
use App\Wallet\Entity\Wallet;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OperandTransactionModel
{
    public int $id = 0;

    /**
     * @Assert\NotBlank
     */
    public Operand $recipient;

    /**
     * @Assert\NotBlank
     * @Assert\Expression(
     *     expression="this.amount !== null && this.amount.isPositive()",
     *     message="Сумма должна быть больше нуля."
     * )
     */
    public ?Money $amount;

    public ?string $description;

    public bool $increment = false;

    public ?Wallet $wallet;

    public function __construct()
    {
        $this->amount = null;
        $this->description = null;
    }
}
