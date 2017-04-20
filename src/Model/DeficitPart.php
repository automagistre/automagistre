<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class DeficitPart extends WarehousePart
{
    /**
     * @var Supply[]
     */
    public $orders;
}
