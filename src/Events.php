<?php

declare(strict_types=1);

namespace App;

interface Events
{
    /**
     * Приход оприходован.
     */
    public const INCOME_ACCRUED = 'income.accrued';

    /**
     * Запчасть из прихода зачислена на склад.
     */
    public const INCOME_PART_ACCRUED = 'income_part.accrued';

    /**
     * На склад поступила запчасть.
     */
    public const PART_ACCRUED = 'part.accrued';

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
}
