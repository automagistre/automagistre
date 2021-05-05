<?php

declare(strict_types=1);

namespace App\Order\Enum;

use Premier\Enum\Enum;
use function in_array;

/**
 * @method string getColor()
 * @method static OrderStatus draft()
 * @method static OrderStatus scheduling()
 * @method static OrderStatus ordering()
 * @method static OrderStatus tracking()
 * @method static OrderStatus notification()
 * @method static OrderStatus working()
 * @method static OrderStatus ready()
 * @method static OrderStatus closed()
 * @method static OrderStatus cancelled()
 * @method bool   isOrdering()
 * @method bool   isTracking()
 * @method bool   isWorking()
 * @method bool   isReady()
 * @method bool   isClosed()
 * @method bool   isCancelled()
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
    private const PAYMENT_WAITING = 12;
    private const CANCELLED = 13;

    protected static array $color = [
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
        self::PAYMENT_WAITING => 'primary',
        self::CANCELLED => 'default',
    ];

    protected static array $name = [
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
        self::PAYMENT_WAITING => 'Ожидает Оплаты',
        self::CANCELLED => 'Отменён',
    ];

    public function isEditable(): bool
    {
        return !in_array($this->toId(), [
            self::CLOSED,
            self::CANCELLED,
        ], true);
    }

    /**
     * @return self[]
     */
    public static function selectable(): array
    {
        return self::all(
            [
                self::CLOSED,
                self::CANCELLED,
            ],
            true,
        );
    }

    public function isSelectable(): bool
    {
        return $this->isEditable();
    }

    public function printable(): bool
    {
        return self::CANCELLED !== $this->toId();
    }
}
