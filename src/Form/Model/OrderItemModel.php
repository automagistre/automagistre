<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItem;

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
