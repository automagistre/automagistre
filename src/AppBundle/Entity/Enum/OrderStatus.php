<?php

namespace AppBundle\Entity\Enum;

use Grachevko\Enum\Enum;

/**
 * @method static OrderStatus draft()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderStatus extends Enum
{
    const DRAFT = 1;
    const SCHEDULING = 2;
    const ORDERING = 3;
    const MATCHING = 4;
    const TRACKING = 5;
    const DELIVERY = 6;
    const NOTIFICATION = 7;
    const WORKING = 8;
    const READY = 9;
    const CLOSED = 10;
}
