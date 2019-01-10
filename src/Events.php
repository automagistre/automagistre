<?php

declare(strict_types=1);

namespace App;

interface Events
{
    /**
     * Статус заказа изменён.
     */
    public const ORDER_STATUS = 'order.status';

    /**
     * Заказ закрыт
     */
    public const ORDER_CLOSED = 'order.closed';

    /**
     * Клиент записан по заказу.
     */
    public const ORDER_APPOINTMENT = 'order.appointment';

    /**
     * Приход оприходован.
     */
    public const INCOME_ACCRUED = 'income.accrued';

    /**
     * Запчасть из прихода зачислена на склад.
     */
    public const INCOME_PART_ACCRUED = 'income_part.accrued';

    /**
     * Создана новая запчасть.
     */
    public const PART_CREATED = 'part.created';

    /**
     * На склад поступила запчасть.
     */
    public const PART_ACCRUED = 'part.accrued';

    /**
     * Запчасть списана со склада.
     */
    public const PART_OUTCOME = 'part.outcome';

    /**
     * Запчасть зарезервирована.
     */
    public const PART_RESERVED = 'part.reserved';

    /**
     * Запчасть снята с резервации.
     */
    public const PART_DERESERVED = 'part.dereserved';

    /**
     * Проводка создана.
     */
    public const PAYMENT_CREATED = 'payment.created';

    /**
     * Создан новый клиент
     */
    public const PERSON_CREATED = 'person.created';

    /**
     * Создана новая организация.
     */
    public const ORGANIZATION_CREATED = 'organization.created';

    /**
     * Создан сотрудник.
     */
    public const EMPLOYEE_CREATED = 'employee.created';

    /**
     * Сотрудник уволен.
     */
    public const EMPLOYEE_FIRED = 'employee.fired';

    /**
     * Создана статья расходов.
     */
    public const EXPENSE_CREATED = 'expense.created';

    /**
     * Создан расход.
     */
    public const EXPENSE_ITEM_CREATED = 'expense.item.created';
}
