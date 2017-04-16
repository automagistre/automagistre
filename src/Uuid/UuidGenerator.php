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
    /**
     * @return UuidInterface
     */
    public static function generate()
    {
        return Uuid::uuid1();
    }
}
