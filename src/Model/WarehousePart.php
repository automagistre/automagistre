<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class WarehousePart extends Model
{
    /**
     * @var \App\Entity\Part
     */
    public $part;

    /**
     * @var int
     */
    public $quantity;
}
