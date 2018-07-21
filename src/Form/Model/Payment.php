<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Operand;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Payment extends Model
{
    /**
     * @var Operand
     */
    public $recipient;

    /**
     * @var string
     */
    public $sender;

    /**
     * @var string
     */
    public $paymentType;

    /**
     * @var Money
     */
    public $amount;

    /**
     * @var string
     */
    public $description;

    public static function getEntityClass(): string
    {
        return \App\Entity\Payment::class;
    }
}
