<?php

declare(strict_types=1);

namespace App\Enum;

use Grachevko\Enum\Enum;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AccountType extends Enum
{
    protected const CASH = 1;
    protected const BONUS = 2;
}
