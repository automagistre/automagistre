<?php

declare(strict_types=1);

namespace App\Order\Form;

use App\Form\Model\Model;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItem;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class OrderItemModel extends Model
{
    public Order $order;

    public ?OrderItem $parent = null;
}
