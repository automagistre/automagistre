<?php

declare(strict_types=1);

namespace App\Order\Number;

use App\Order\Entity\Order;
use App\Shared\Doctrine\Registry;

final class NumberGenerator
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function next(): string
    {
        return (string) $this->registry->connection(Order::class)
            ->fetchColumn('SELECT nextval(\'order_number\')');
    }
}
