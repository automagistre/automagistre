<?php

declare(strict_types=1);

namespace App\Model;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class EntityModel extends Model
{
    /**
     * @var UuidInterface
     */
    public $id;

    abstract public static function getEntityClass(): string;
}
