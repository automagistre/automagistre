<?php

declare(strict_types=1);

namespace App\Appeal\Enum;

use App\Appeal\Entity\Calculator;
use App\Appeal\Entity\Call;
use App\Appeal\Entity\Cooperation;
use App\Appeal\Entity\Question;
use App\Appeal\Entity\Schedule;
use App\Appeal\Entity\TireFitting;
use Premier\Enum\Enum;

/**
 * @method string toDisplayName()
 * @method string toEntityClass()
 *
 * @psalm-immutable
 */
final class AppealType extends Enum
{
    private const CALCULATOR = 1;
    private const COOPERATION = 2;
    private const QUESTION = 3;
    private const SCHEDULE = 4;
    private const TIRE_FITTING = 5;
    private const CALL = 6;

    protected static array $displayName = [
        self::CALCULATOR => 'Калькулятор',
        self::COOPERATION => 'Сотрудничество',
        self::QUESTION => 'Вопрос',
        self::SCHEDULE => 'Запись',
        self::TIRE_FITTING => 'Шиномонтаж',
        self::CALL => 'Звонок',
    ];

    protected static array $entityClass = [
        self::CALCULATOR => Calculator::class,
        self::COOPERATION => Cooperation::class,
        self::QUESTION => Question::class,
        self::SCHEDULE => Schedule::class,
        self::TIRE_FITTING => TireFitting::class,
        self::CALL => Call::class,
    ];
}
