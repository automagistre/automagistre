<?php

declare(strict_types=1);

namespace App\Model;

use Money\Money;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SupplyItem extends Model
{
    /**
     * @var string
     */
    public $number;

    /**
     * @var string
     */
    public $manufacturer;

    /**
     * @var string
     */
    public $name;

    /**
     * @var Money
     */
    public $price;

    /**
     * @var int
     */
    public $quantity;
}
