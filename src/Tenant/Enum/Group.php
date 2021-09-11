<?php

declare(strict_types=1);

namespace App\Tenant\Enum;

use Premier\Enum\Enum;

/**
 * @method static self  demo()
 * @method static self  automagistre()
 * @method static self  shavlev()
 */
final class Group extends Enum
{
    private const DEMO = 0;
    private const AUTOMAGISTRE = 1;
    private const SHAVLEV = 2;
}
