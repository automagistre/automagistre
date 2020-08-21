<?php

declare(strict_types=1);

namespace App\Part\Enum;

use Premier\Enum\Enum;

/**
 * @psalm-immutable
 *
 * @method static self subzeroQuantity()
 * @method static self ok()
 * @method static self needSupplyForOrder()
 * @method static self needSupplyForStock()
 */
final class WhatToBuyStatus extends Enum
{
    private const SUBZERO_QUANTITY = 1;
    private const OK = 2;
    private const NEED_SUPPLY_FOR_ORDER = 3;
    private const NEED_SUPPLY_FOR_STOCK = 4;

    protected static array $description = [
        self::SUBZERO_QUANTITY => 'Отрицательное количество на складе',
        self::OK => 'Заказано',
        self::NEED_SUPPLY_FOR_ORDER => 'Нужна поставка для заказа',
        self::NEED_SUPPLY_FOR_STOCK => 'Нужно восполнить запасы',
    ];

    protected static array $color = [
        self::SUBZERO_QUANTITY => 'danger',
        self::OK => 'success',
        self::NEED_SUPPLY_FOR_ORDER => 'warning',
        self::NEED_SUPPLY_FOR_STOCK => 'warning',
    ];

    protected static array $icon = [
        self::SUBZERO_QUANTITY => 'exclamation-triangle',
        self::OK => 'check',
        self::NEED_SUPPLY_FOR_ORDER => 'fire',
        self::NEED_SUPPLY_FOR_STOCK => 'cubes',
    ];

    protected static array $sort = [
        self::SUBZERO_QUANTITY => 1,
        self::OK => 4,
        self::NEED_SUPPLY_FOR_ORDER => 2,
        self::NEED_SUPPLY_FOR_STOCK => 3,
    ];
}
