<?php

declare(strict_types=1);

namespace App\Wallet\Enum;

use App\Customer\Entity\TransactionId;
use App\Expense\Entity\ExpenseId;
use App\Income\Entity\IncomeId;
use App\Order\Entity\OrderId;
use App\User\Entity\UserId;
use Premier\Enum\Enum;

/**
 * @method static self legacy()
 * @method static self orderPrepay()
 * @method static self orderDebit()
 * @method static self payroll()
 * @method static self incomePayment()
 * @method static self expense()
 * @method static self operandManual()
 */
final class TransactionSource extends Enum
{
    /**
     * Какие то старые проводки
     * Связать со статьями расходов и проводками клиентов можно только вручную
     */
    private const LEGACY = 0;
    /**
     * Предоплата по заказу.
     */
    private const ORDER_PREPAY = 1;
    /**
     * Начисление по заказу.
     */
    private const ORDER_DEBIT = 2;
    /**
     * Выдача зарплаты.
     */
    private const PAYROLL = 3;
    /**
     * Оплата за поставку.
     */
    private const INCOME_PAYMENT = 4;
    /**
     * Списание по статье расходов.
     */
    private const EXPENSE = 5;
    /**
     * Ручная проводка клиента
     */
    private const OPERAND_MANUAL = 6;

    protected static array $sourceClass = [
        self::LEGACY => UserId::class,
        self::ORDER_PREPAY => OrderId::class,
        self::ORDER_DEBIT => OrderId::class,
        self::PAYROLL => UserId::class,
        self::INCOME_PAYMENT => IncomeId::class,
        self::EXPENSE => ExpenseId::class,
        self::OPERAND_MANUAL => TransactionId::class,
    ];
}
