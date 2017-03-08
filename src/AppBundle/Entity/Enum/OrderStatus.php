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

    protected static $color = [
        self::DRAFT => 'default',
        self::SCHEDULING => 'info',
        self::TRACKING => 'default',
        self::NOTIFICATION => 'warning',
        self::WORKING => 'success',
        self::READY => 'warning',
        self::CLOSED => 'default',
    ];

    protected static $name = [
        self::DRAFT => 'Черновик',
        self::SCHEDULING => 'Ожидание по записи',
        self::TRACKING => 'Ожидание запчастей',
        self::NOTIFICATION => 'Уведомление клиента',
        self::WORKING => 'В работе',
        self::READY => 'Ожидает выдачи',
        self::CLOSED => 'Закрыт',
    ];

    public function isEditable(): bool
    {
        return !$this->in([
            self::CLOSED,
        ]);
    }
}
