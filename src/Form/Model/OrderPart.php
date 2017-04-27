<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\OrderItemPart;
use App\Entity\Part;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderPart extends OrderItemModel
{
    /**
     * @var Part
     */
    public $part;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var Money
     */
    public $price;

    public static function getEntityClass(): string
    {
        return OrderItemPart::class;
    }
}
