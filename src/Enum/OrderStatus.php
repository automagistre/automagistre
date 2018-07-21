<?php

declare(strict_types=1);

namespace App\Enum;

use Grachevko\Enum\Enum;

/**
 * @method static OrderStatus draft()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderStatus extends Enum
{
    private const DRAFT = 1;
    private const SCHEDULING = 2;
    private const ORDERING = 3;
    private const MATCHING = 4;
    private const TRACKING = 5;
    private const DELIVERY = 6;
    private const NOTIFICATION = 7;
    private const WORKING = 8;
    private const READY = 9;
    private const CLOSED = 10;

    /**
     * @var array
     */
    protected static $color = [
        self::DRAFT => 'default',
        self::SCHEDULING => 'info',
        self::ORDERING => 'info',
        self::MATCHING => 'primary',
        self::TRACKING => 'default',
        self::DELIVERY => 'primary',
        self::NOTIFICATION => 'warning',
        self::WORKING => 'success',
        self::READY => 'warning',
        self::CLOSED => 'default',
    ];

    /**
     * @var array
     */
    protected static $name = [
        self::DRAFT => 'Черновик',
        self::SCHEDULING => 'Ожидание по записи',
        self::ORDERING => 'Заказ запчастей',
        self::MATCHING => 'MATCHING',
        self::TRACKING => 'Ожидание запчастей',
        self::DELIVERY => 'DELIVERY',
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
