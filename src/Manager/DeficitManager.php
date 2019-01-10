<?php

declare(strict_types=1);

namespace App\Manager;

use App\Doctrine\Registry;
use App\Entity\Landlord\Manufacturer;
use App\Entity\Landlord\Part;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItemPart;
use App\Model\DeficitPart;
use Ramsey\Uuid\Uuid;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class DeficitManager
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return DeficitPart[]
     */
    public function findDeficit(): array
    {
        $sql = <<<SQL
SELECT
  ordered.part_uuid,
  ordered.quantity - COALESCE(stock.quantity, 0) AS needed,
  ordered.orders_id
FROM (SELECT
        order_item_part.part_uuid,
        SUM(order_item_part.quantity) AS quantity,
        GROUP_CONCAT(DISTINCT orders.id, ',') AS orders_id
      FROM order_item_part
        JOIN order_item ON order_item.id = order_item_part.id
        JOIN orders ON order_item.order_id = orders.id AND orders.closed_at IS NULL
      GROUP BY order_item_part.part_uuid) AS ordered
  LEFT JOIN (SELECT
               motion.part_uuid,
               SUM(motion.quantity) AS quantity
             FROM motion
               JOIN (SELECT
                       DISTINCT order_item_part.part_uuid
                     FROM order_item_part
                       JOIN order_item ON order_item.id = order_item_part.id
                       JOIN orders ON order_item.order_id = orders.id AND orders.closed_at IS NULL
                    ) AS parts ON motion.part_uuid = parts.part_uuid
             GROUP BY motion.part_uuid) AS stock ON stock.part_uuid = ordered.part_uuid
HAVING needed > 0;
SQL;

        $em = $this->registry->manager(OrderItemPart::class);
        $conn = $em->getConnection();

        $result = $conn->fetchAll($sql);

        $partRepository = $this->registry->repository(Part::class);
        $parts = $partRepository->findBy([
            'uuid' => \array_map(function (array $item) {
                return $item['part_uuid'];
            }, $result),
        ]);

        $this->registry->repository(Manufacturer::class)->findBy([
            'id' => \array_map(function (Part $part) {
                return $part->getManufacturer()->getId();
            }, $parts),
        ]);

        return \array_map(function (array $item) use ($em, $partRepository) {
            return new DeficitPart([
                'part' => $partRepository->findOneBy(['uuid' => Uuid::fromBytes($item['part_uuid'])]),
                'quantity' => $item['needed'],
                'orders' => \array_map(function (int $id) use ($em) {
                    return $em->getReference(Order::class, $id);
                }, \array_filter(\explode(',', $item['orders_id']), 'strlen')),
            ]);
        }, $result);
    }
}
