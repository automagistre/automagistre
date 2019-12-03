<?php

declare(strict_types=1);

namespace App\Manager;

use App\Doctrine\Registry;
use App\Entity\Landlord\Part;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItemPart;
use App\Manufacturer\Entity\Manufacturer;
use App\Model\DeficitPart;
use function array_filter;
use function array_map;
use function explode;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class DeficitManager
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return DeficitPart[]
     */
    public function findDeficit(): array
    {
        $sql = <<<'SQL'
            SELECT
              ordered.part_id,
              ordered.quantity - COALESCE(stock.quantity, 0) AS needed,
              ordered.orders_id
            FROM (SELECT
                    order_item_part.part_id,
                    SUM(order_item_part.quantity) AS quantity,
                    GROUP_CONCAT(DISTINCT orders.id, ',') AS orders_id
                  FROM order_item_part
                    JOIN order_item ON order_item.id = order_item_part.id
                    JOIN orders ON order_item.order_id = orders.id AND orders.closed_at IS NULL
                  GROUP BY order_item_part.part_id) AS ordered
              LEFT JOIN (SELECT
                           motion.part_id,
                           SUM(motion.quantity) AS quantity
                         FROM motion
                           JOIN (SELECT
                                   DISTINCT order_item_part.part_id
                                 FROM order_item_part
                                   JOIN order_item ON order_item.id = order_item_part.id
                                   JOIN orders ON order_item.order_id = orders.id AND orders.closed_at IS NULL
                                ) AS parts ON motion.part_id = parts.part_id
                         GROUP BY motion.part_id) AS stock ON stock.part_id = ordered.part_id
            HAVING needed > 0;
            SQL;

        $em = $this->registry->manager(OrderItemPart::class);
        $conn = $em->getConnection();

        $result = $conn->fetchAll($sql);

        $partRepository = $this->registry->repository(Part::class);
        $parts = $partRepository->findBy([
            'id' => array_map(fn (array $item): string => $item['part_id'], $result),
        ]);

        $this->registry->repository(Manufacturer::class)->findBy([
            'id' => array_map(fn (Part $part) => $part->getManufacturer()->getId(), $parts),
        ]);

        return array_map(static function (array $item) use ($em, $partRepository): DeficitPart {
            /** @var Part $part */
            $part = $partRepository->find($item['part_id']);
            $quantity = $item['needed'];
            $orders = array_map(fn (string $id): object => $em->getReference(Order::class, $id), array_filter(explode(',', $item['orders_id'])));

            return new DeficitPart($part, $quantity, $orders);
        }, $result);
    }
}
