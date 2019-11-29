<?php

declare(strict_types=1);

namespace App\Form\Model;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class Model
{
    public int $id;

    abstract public static function getEntityClass(): string;
}
