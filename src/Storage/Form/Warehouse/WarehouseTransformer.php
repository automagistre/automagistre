<?php

declare(strict_types=1);

namespace App\Storage\Form\Warehouse;

use App\Doctrine\Registry;
use App\Storage\Entity\WarehouseId;
use App\Storage\Entity\WarehouseView;
use Symfony\Component\Form\CallbackTransformer;

final class WarehouseTransformer
{
    public static function create(Registry $registry): CallbackTransformer
    {
        return new CallbackTransformer(
            function (?WarehouseId $warehouseId) use ($registry): ?WarehouseView {
                return null === $warehouseId ? null : $registry->get(WarehouseView::class, $warehouseId);
            },
            fn (?WarehouseView $view) => null === $view ? null : $view->id,
        );
    }
}
