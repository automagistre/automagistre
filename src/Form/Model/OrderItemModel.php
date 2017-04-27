<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Order;
use App\Entity\OrderItem;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class OrderItemModel extends Model
{
    /**
     * @var Order
     */
    public $order;

    /**
     * @var OrderItem
     */
    public $parent;
}
