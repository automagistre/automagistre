<?php

declare(strict_types=1);

namespace App\Form\Model;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Payment extends Model
{
    public $recipient;
    public $sender;
    public $paymentType;
    public $amount;
    public $description;

    public static function getEntityClass(): string
    {
        return \App\Entity\Payment::class;
    }
}
