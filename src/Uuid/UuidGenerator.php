<?php

declare(strict_types=1);

namespace App\Uuid;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UuidGenerator
{
    public static function generate(): UuidInterface
    {
        return Uuid::uuid1();
    }
}
