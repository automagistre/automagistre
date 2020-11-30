<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="warehouse_view")
 */
class WarehouseView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="warehouse_id")
     */
    public WarehouseId $id;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column(type="warehouse_id", nullable=true)
     */
    public ?WarehouseId $parentId = null;

    private function __construct(WarehouseId $id, string $name, WarehouseId $parentId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->parentId = $parentId;
    }

    public function toId(): WarehouseId
    {
        return $this->id;
    }

    public static function sql(): string
    {
        return '
            CREATE VIEW warehouse_view AS
            SELECT root.id                AS id,
                   wn.name                AS name,
                   wp.warehouse_parent_id AS parent_id
            FROM warehouse root
                     JOIN LATERAL (SELECT name
                                   FROM warehouse_name sub
                                   WHERE sub.warehouse_id = root.id
                                   ORDER BY sub.id DESC
                                   LIMIT 1
                ) wn ON true
                     LEFT JOIN LATERAL (SELECT warehouse_parent_id
                                        FROM warehouse_parent sub
                                        WHERE sub.warehouse_id = root.id
                                        ORDER BY sub.id DESC
                                        LIMIT 1
                ) wp ON true        
        ';
    }
}
