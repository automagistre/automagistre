<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Part;
use App\Model\WarehousePart;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartManager
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(Connection $conn, EntityManager $em)
    {
        $this->conn = $conn;
        $this->em = $em;
    }

    /**
     * @return WarehousePart[]
     */
    public function findDeficit(): array
    {
        $sql = <<<SQL
SELECT
  ordered.part_id,
  ordered.quantity + COALESCE(moved.quantity, 0) - COALESCE(stock.quantity, 0) AS needed
FROM (SELECT
        order_part.part_id,
        SUM(order_part.quantity) AS quantity
      FROM order_part
        JOIN orders ON order_part.order_id = orders.id AND orders.closed_at IS NULL
      GROUP BY order_part.part_id) AS ordered
  LEFT JOIN (SELECT
               motion.part_id,
               SUM(motion.quantity) AS quantity
             FROM motion
               JOIN orders ON motion.order_id = orders.id AND orders.closed_at IS NULL
             GROUP BY motion.part_id) AS moved ON ordered.part_id = moved.part_id
  LEFT JOIN (SELECT
               motion.part_id,
               SUM(motion.quantity) AS quantity
             FROM motion
               JOIN (SELECT
                       DISTINCT order_part.part_id AS id
                     FROM order_part
                       JOIN orders ON order_part.order_id = orders.id AND orders.closed_at IS NULL
                    ) AS parts ON motion.part_id = parts.id
             GROUP BY motion.part_id) AS stock ON stock.part_id = ordered.part_id
HAVING needed > 0
SQL;

        $result = $this->conn->fetchAll($sql);

        $partRepository = $this->em->getRepository(Part::class);
        $partRepository->findBy([
            'id' => array_map(function (array $item) {
                return $item['part_id'];
            }, $result),
        ]);

        return array_map(function (array $item) use ($partRepository) {
            return new WarehousePart([
                'part'     => $partRepository->find($item['part_id']),
                'quantity' => $item['needed'],
            ]);
        }, $result);
    }
}
