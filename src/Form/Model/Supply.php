<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Operand;
use App\Entity\Part;
use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Supply extends Model
{
    /**
     * @var Operand
     */
    public $supplier;

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
        return \App\Entity\Supply::class;
    }

    public static function createFromSupply(\App\Entity\Supply $supply): self
    {
        $model = new self();
        $model->supplier = $supply->getSupplier();
        $model->part = $supply->getPart();
        $model->price = $supply->getPrice();
        $model->quantity = $supply->getQuantity();

        return $model;
    }
}
