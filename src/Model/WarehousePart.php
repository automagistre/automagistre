<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Landlord\Part;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class WarehousePart
{
    /**
     * @var Part
     */
    public $part;

    /**
     * @var int
     */
    public $quantity;

    public function __construct(Part $part, int $quantity)
    {
        $this->part = $part;
        $this->quantity = $quantity;
    }
}
