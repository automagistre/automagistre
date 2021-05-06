<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Part\Entity\PartId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 *
 * @psalm-suppress MissingConstructor
 */
class InventorizationPartView
{
    /**
     * @ORM\Column(type="inventorization_id")
     */
    public InventorizationId $inventorizationId;

    /**
     * @ORM\Id()
     * @ORM\Column(type="part_id")
     */
    public PartId $partId;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity;

    /**
     * @ORM\Column(type="integer")
     */
    public int $inStock;

    /**
     * @ORM\Column(type="integer")
     */
    public int $reserved;

    public static function sql(): string
    {
        return <<<'SQL'
            CREATE VIEW inventorization_part_view AS
            SELECT
                   ip.inventorization_id,
                   ip.part_id,
                   ip.quantity,
                   COALESCE(stock.quantity, 0) AS in_stock,
                   COALESCE(reserved.quantity, 0) AS reserved
            FROM inventorization_part ip
            LEFT JOIN (SELECT motion.part_id, SUM(motion.quantity) AS quantity FROM motion GROUP BY motion.part_id) stock
                   ON stock.part_id = ip.part_id
            LEFT JOIN (SELECT order_item_part.part_id, SUM(reservation.quantity) AS quantity
                    FROM reservation
                             JOIN order_item_part ON order_item_part.id = reservation.order_item_part_id
                    GROUP BY order_item_part.part_id) AS reserved
                   ON reserved.part_id = ip.part_id
            SQL;
    }
}
