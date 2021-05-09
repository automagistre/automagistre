<?php

declare(strict_types=1);

namespace App\Order\Number;

use App\Order\Entity\Order;
use App\Shared\Doctrine\Registry;

final class NumberGenerator
{
    public function __construct(private Registry $registry)
    {
    }

    public function next(): string
    {
        return (string) $this->registry->connection(Order::class)
            ->fetchOne('SELECT nextval(\'order_number\')')
        ;
    }
}
