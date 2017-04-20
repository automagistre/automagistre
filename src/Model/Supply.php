<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Operand;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Supply extends EntityModel
{
    /**
     * @var Operand
     */
    public $supplier;

    /**
     * @var \App\Entity\Part
     */
    public $part;

    /**
     * @var int
     */
    public $quantity;

    public static function getEntityClass(): string
    {
        return \App\Entity\Supply::class;
    }
}
