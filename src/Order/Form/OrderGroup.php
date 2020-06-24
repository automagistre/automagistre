<?php

declare(strict_types=1);

namespace App\Order\Form;

use App\Order\Entity\OrderItemGroup;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderGroup extends OrderItemModel
{
    public ?string $name;

    public bool $hideParts = false;

    public function __construct()
    {
        $this->name = null;
    }

    public static function getEntityClass(): string
    {
        return OrderItemGroup::class;
    }
}
