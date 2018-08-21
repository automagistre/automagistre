<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Manufacturer;
use App\Entity\Order;
use App\Entity\Part;
use App\Model\DeficitPart;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class DeficitManager
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
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
  ordered.part_id,
  ordered.quantity - COALESCE(stock.quantity, 0) - COALESCE(supply.quantity, 0) AS needed,
  ordered.orders_id
FROM (SELECT
        order_item_part.part_id,
        SUM(order_item_part.quantity)              AS quantity,
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
  LEFT JOIN (SELECT DISTINCT
               supply.part_id,
               SUM(supply.quantity) AS quantity
             FROM supply
    GROUP BY supply.part_id
    ) AS supply ON supply.part_id = ordered.part_id
HAVING needed > 0;
SQL;

        $em = $this->registry->getEntityManager();
        $conn = $em->getConnection();

        $result = $conn->fetchAll($sql);

        $partRepository = $em->getRepository(Part::class);
        $parts = $partRepository->findBy([
            'id' => array_map(function (array $item) {
                return $item['part_id'];
            }, $result),
        ]);

        $em->getRepository(Manufacturer::class)->findBy([
            'id' => array_map(function (Part $part) {
                return $part->getManufacturer()->getId();
            }, $parts),
        ]);

        return array_map(function (array $item) use ($em, $partRepository) {
            return new DeficitPart([
                'part' => $partRepository->find($item['part_id']),
                'quantity' => $item['needed'],
                'orders' => array_map(function (int $id) use ($em) {
                    return $em->getReference(Order::class, $id);
                }, array_filter(explode(',', $item['orders_id']), 'strlen')),
            ]);
        }, $result);
    }
}
