<?php

declare(strict_types=1);

namespace App\Order\Number;

use App\Doctrine\Registry;
use App\Order\Entity\Order;
use function assert;
use function is_int;

final class NumberGenerator
{
    public function __construct(private Registry $registry)
    {
    }

    public function next(): int
    {
        $number = $this->registry->manager()->createQueryBuilder()
            ->select('MAX(t.number)')
            ->from(Order::class, 't')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        assert(is_int($number));

        return ++$number;
    }
}
