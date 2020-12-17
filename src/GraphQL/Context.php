<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\Shared\Doctrine\Registry;

/**
 * @psalm-immutable
 */
abstract class Context
{
    public Registry $registry;

    public Buffer $buffer;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
        $this->buffer = new Buffer($registry);
    }
}
