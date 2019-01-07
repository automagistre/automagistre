<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Order;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class DeficitPart extends WarehousePart
{
    /**
     * @var Order[]
     */
    public $orders;
}
