<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Operand;
use App\Entity\Part;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Income extends Model
{
    /**
     * @var Operand
     */
    public $supplier;

    /**
     * @var Part[]
     */
    public $parts;

    public static function getEntityClass(): string
    {
        return \App\Entity\Income::class;
    }
}
