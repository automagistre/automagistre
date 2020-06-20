<?php

declare(strict_types=1);

namespace App\Customer\Enum;

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
 */
final class TransactionSource extends Enum
{
    /**
     * Предоплата по заказу.
     */
    private const ORDER_PREPAY = 1;
    /**
     * Начисление по заказу.
     */
    private const ORDER_DEBIT = 2;
    /**
     * Списание по заказу.
     */
    private const ORDER_PAYMENT = 3;
    /**
     * ЗП По заказу.
     */
    private const ORDER_SALARY = 4;
    /**
     * Выдача зарплаты.
     */
    private const PAYROLL = 5;
    /**
     * Начисление по поставке.
     */
    private const INCOME_DEBIT = 6;
    /**
     * Оплата за поставку.
     */
    private const INCOME_PAYMENT = 7;
    /**
     * Начисление ежемесячного оклада.
     */
    private const SALARY = 8;
    /**
     * Штраф работника.
     */
    private const PENALTY = 9;
    /**
     * Ручная проводка.
     */
    private const MANUAL = 10;
}
