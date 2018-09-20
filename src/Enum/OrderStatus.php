<?php

declare(strict_types=1);

namespace App\Enum;

use Grachevko\Enum\Enum;

/**
 * @method string getColor()
 * @method static OrderStatus draft()
 * @method static OrderStatus working()
 * @method static OrderStatus closed()
 * @method bool   isClosed()
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
    private const SELECTION = 11;

    /**
     * @var array
     */
    protected static $color = [
        self::DRAFT => 'default',
        self::SCHEDULING => 'primary',
        self::ORDERING => 'danger',
        self::MATCHING => 'warning',
        self::TRACKING => 'default',
        self::DELIVERY => 'info',
        self::NOTIFICATION => 'warning',
        self::WORKING => 'success',
        self::READY => 'primary',
        self::CLOSED => 'default',
        self::SELECTION => 'danger',
    ];

    /**
     * @var array
     */
    protected static $name = [
        self::DRAFT => 'Черновик',
        self::SCHEDULING => 'Ожидание по записи',
        self::ORDERING => 'Заказ запчастей',
        self::MATCHING => 'Согласование',
        self::TRACKING => 'Ожидание запчастей',
        self::DELIVERY => 'Требуется доставка',
        self::NOTIFICATION => 'Уведомление клиента',
        self::WORKING => 'В работе',
        self::READY => 'Ожидает выдачи',
        self::CLOSED => 'Закрыт',
        self::SELECTION => 'Подбор запчастей',
    ];

    public function isEditable(): bool
    {
        return !$this->in([
            self::CLOSED,
        ]);
    }

    /**
     * @return self[]
     */
    public static function selectable(): array
    {
        return self::all([self::CLOSED], true);
    }

    public function isSelectable(): bool
    {
        return $this->isEditable();
    }
}
