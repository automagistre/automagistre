<?php

declare(strict_types=1);

namespace App\Sms\Enum;

use Premier\Enum\Enum;

/**
 * @method static self onceADay()
 */
final class Feature extends Enum
{
    private const ONCE_A_DAY = 1;
}
