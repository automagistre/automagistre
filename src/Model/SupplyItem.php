<?php

declare(strict_types=1);

namespace App\Model;

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
    public $maker;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $price;
}
