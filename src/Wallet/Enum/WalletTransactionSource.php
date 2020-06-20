<?php

declare(strict_types=1);

namespace App\Wallet\Enum;

use App\Customer\Entity\CustomerTransactionId;
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
 * @method string toDescription()
 */
final class WalletTransactionSource extends Enum
{
    /**
     * Связать со статьями расходов и проводками клиентов можно только вручную.
     */
    private const LEGACY = 0;
    private const ORDER_PREPAY = 1;
    private const ORDER_DEBIT = 2;
    private const PAYROLL = 3;
    private const INCOME_PAYMENT = 4;
    private const EXPENSE = 5;
    private const OPERAND_MANUAL = 6;

    protected static array $description = [
        self::LEGACY => 'Какие то старые проводки',
        self::ORDER_PREPAY => 'Предоплата по заказу',
        self::ORDER_DEBIT => 'Начисление по заказу',
        self::PAYROLL => 'Выдача зарплаты',
        self::INCOME_PAYMENT => 'Оплата за поставку',
        self::EXPENSE => 'Списание по статье расходов',
        self::OPERAND_MANUAL => 'Ручная проводка клиента',
    ];

    protected static array $sourceClass = [
        self::LEGACY => UserId::class,
        self::ORDER_PREPAY => OrderId::class,
        self::ORDER_DEBIT => OrderId::class,
        self::PAYROLL => CustomerTransactionId::class,
        self::INCOME_PAYMENT => IncomeId::class,
        self::EXPENSE => ExpenseId::class,
        self::OPERAND_MANUAL => CustomerTransactionId::class,
    ];
}
