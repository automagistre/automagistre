<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\Shared\Doctrine\Registry;

/**
 * @psalm-immutable
 */
abstract class Context
{
    public Buffer $buffer;

    public function __construct(public Registry $registry)
    {
        $this->buffer = new Buffer($registry);
    }
}
