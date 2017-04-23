<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Part;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class IncomePart extends Model
{
    /**
     * @var Part
     */
    public $part;

    /**
     * @var Money
     */
    public $price;

    /**
     * @var int
     */
    public $quantity;

    public static function getEntityClass(): string
    {
        return \App\Entity\IncomePart::class;
    }
}
