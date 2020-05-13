<?php

declare(strict_types=1);

namespace App\Shared\Enum;

use Premier\Enum\Enum;

/**
 * @method static self promote()
 * @method static self demote()
 */
final class Transition extends Enum
{
    private const DEMOTE = -1;
    private const PROMOTE = -1;
}
