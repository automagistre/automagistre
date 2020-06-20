<?php

declare(strict_types=1);

namespace App\Customer\Enum;

use App\Income\Entity\IncomeId;
use App\Order\Entity\OrderId;
use App\User\Entity\UserId;
use Premier\Enum\Enum;

/**
 * @method static self orderPrepay()
 * @method static self orderDebit()
 * @method static self orderPayment()
 * @method static self orderSalary()
 * @method static self payroll()
 * @method static self incomeDebit()
 * @method static self incomePayment()
 * @method static self salary()
 * @method static self penalty()
 * @method static self manual()
 * @method string toDescription()
 */
final class CustomerTransactionSource extends Enum
{
    private const ORDER_PREPAY = 1;
    private const ORDER_DEBIT = 2;
    private const ORDER_PAYMENT = 3;
    private const ORDER_SALARY = 4;
    private const PAYROLL = 5;
    private const INCOME_DEBIT = 6;
    private const INCOME_PAYMENT = 7;
    private const SALARY = 8;
    private const PENALTY = 9;
    private const MANUAL = 10;

    protected static array $sourceClass = [
        self::ORDER_PREPAY => OrderId::class,
        self::ORDER_DEBIT => OrderId::class,
        self::ORDER_PAYMENT => OrderId::class,
        self::ORDER_SALARY => OrderId::class,
        self::PAYROLL => UserId::class,
        self::INCOME_DEBIT => IncomeId::class,
        self::INCOME_PAYMENT => IncomeId::class,
        self::SALARY => OrderId::class,
        self::PENALTY => UserId::class,
        self::MANUAL => UserId::class,
    ];

    protected static array $description = [
        self::ORDER_PREPAY => 'Предоплата по заказу',
        self::ORDER_DEBIT => 'Начисление по заказу',
        self::ORDER_PAYMENT => 'Списание по заказу',
        self::ORDER_SALARY => 'Зарплата по заказу',
        self::PAYROLL => 'Выдача зарплаты',
        self::INCOME_DEBIT => 'Начисление по поставке',
        self::INCOME_PAYMENT => 'Оплата за поставку',
        self::SALARY => 'Начисление ежемесячного оклада',
        self::PENALTY => 'Штраф работника',
        self::MANUAL => 'Ручная проводка',
    ];
}
