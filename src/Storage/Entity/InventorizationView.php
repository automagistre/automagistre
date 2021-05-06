<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 *
 * @psalm-immutable
 *
 * @psalm-suppress MissingConstructor
 */
class InventorizationView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="inventorization_id")
     */
    public InventorizationId $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    public ?DateTimeImmutable $closedAt = null;

    public static function sql(): string
    {
        return <<<'SQL'
            CREATE VIEW inventorization_view AS
            SELECT
                   i.id,
                   cb.created_at,
                   cbc.created_at AS closed_at
            FROM inventorization i
                JOIN created_by cb ON cb.id = i.id
                LEFT JOIN inventorization_close ic ON i.id = ic.inventorization_id
                LEFT JOIN created_by cbc ON cbc.id = ic.id
            SQL;
    }
}
