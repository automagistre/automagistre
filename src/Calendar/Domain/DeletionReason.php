<?php

declare(strict_types=1);

namespace App\Calendar\Domain;

use Premier\Enum\Enum;

/**
 * @psalm-immutable
 */
final class DeletionReason extends Enum
{
    private const NO_REASON = 1;
    private const PLAN_ANOTHER_TIME = 2;
    private const NOT_HAVE_TIME_TO_ARRIVE = 3;
    private const SOLVE_PROBLEM_WITHOUT_SERVICE = 4;
    private const WE_ARE_CONDOMS = 5;

    protected static array $name = [
        self::NO_REASON => 'Клиент отменил запись без причины',
        self::PLAN_ANOTHER_TIME => 'Клиент планирует записаться на другое время',
        self::NOT_HAVE_TIME_TO_ARRIVE => 'Клиент не успевает приехать',
        self::SOLVE_PROBLEM_WITHOUT_SERVICE => 'Клиент решил проблему до приезда в сервис',
        self::WE_ARE_CONDOMS => 'Клиент где то узнал что мы гондоны и не приехал',
    ];
}
