<?php

declare(strict_types=1);

namespace App\Entity\Enum;

use Grachevko\Enum\Enum;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AccountType extends Enum
{
    private const CASH = 1;
    private const BONUS = 2;
}
