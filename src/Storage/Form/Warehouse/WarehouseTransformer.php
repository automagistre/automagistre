<?php

declare(strict_types=1);

namespace App\Storage\Form\Warehouse;

use App\Shared\Doctrine\Registry;
use App\Storage\Entity\WarehouseId;
use App\Storage\Entity\WarehouseView;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use function dump;

final class WarehouseTransformer
{
    public static function create(Registry $registry): DataTransformerInterface
    {
        return new CallbackTransformer(
            function (?WarehouseId $warehouseId) use ($registry): ?WarehouseView {
                dump($warehouseId);

                return null === $warehouseId ? null : $registry->get(WarehouseView::class, $warehouseId);
            },
            fn (?WarehouseView $view) => null === $view ? null : $view->id,
        );
    }
}
