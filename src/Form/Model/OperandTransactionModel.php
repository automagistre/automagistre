<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Landlord\Operand;
use App\Entity\Tenant\Wallet;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OperandTransactionModel
{
    /**
     * @var int|null
     */
    public $id;

    /**
     * @var Operand
     *
     * @Assert\NotBlank
     */
    public $recipient;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var Money
     */
    public $amount;

    /**
     * @var bool
     */
    public $increment = false;

    /**
     * @var Wallet|null
     */
    public $wallet;

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->amount->isNegative()) {
            $context->buildViolation('Сумма должна быть больше нуля.')
                ->atPath('amount')
                ->addViolation();
        }
    }
}
