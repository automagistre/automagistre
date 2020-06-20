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
final class TransactionDto
{
    /**
     * @var Operand
     *
     * @Assert\NotBlank
     */
    public $recipient;

    /**
     * @var Money
     *
     * @Assert\NotBlank
     * @Assert\Expression(
     *     expression="this.amount !== null && this.amount.isPositive()",
     *     message="Сумма должна быть больше нуля."
     * )
     */
    public $amount;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var bool
     */
    public $increment = false;

    /**
     * @var Wallet|null
     */
    public $wallet;

    private function __construct(
        Operand $recipient,
        Money $amount,
        ?string $description,
        bool $increment,
        ?Wallet $wallet
    ) {
        $this->recipient = $recipient;
        $this->amount = $amount;
        $this->description = $description;
        $this->increment = $increment;
        $this->wallet = $wallet;
    }
}
