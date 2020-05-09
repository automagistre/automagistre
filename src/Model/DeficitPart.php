<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Tenant\Order;
use App\Part\Domain\Part;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class DeficitPart extends WarehousePart
{
    /**
     * @var Order[]
     */
    public array $orders;

    public function __construct(Part $part, int $quantity, array $orders)
    {
        parent::__construct($part, $quantity);

        $this->orders = $orders;
    }
}
