<?php

declare(strict_types=1);

namespace App\Form\Model;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class Model extends \App\Model\Model
{
    /**
     * @var UuidInterface
     */
    public $id;

    abstract public static function getEntityClass(): string;
}
