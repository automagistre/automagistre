<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Part;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class WarehousePart extends Model
{
    /**
     * @var Part
     */
    public $part;

    /**
     * @var int
     */
    public $quantity;
}
