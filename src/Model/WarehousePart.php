<?php

namespace App\Model;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WarehousePart extends Model
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
