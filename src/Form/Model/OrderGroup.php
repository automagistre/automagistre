<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\OrderItemGroup;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderGroup extends OrderItemModel
{
    /**
     * @var string
     */
    public $name;

    public static function getEntityClass(): string
    {
        return OrderItemGroup::class;
    }
}
