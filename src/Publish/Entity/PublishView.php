<?php

declare(strict_types=1);

namespace App\Publish\Entity;

use App\CreatedBy\Entity\CreatedByView;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="publish_view")
 *
 * @psalm-suppress MissingConstructor
 */
class PublishView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $published;

    /**
     * @ORM\Column(type="created_by_view")
     */
    public CreatedByView $created;

    public static function sql(): string
    {
        return <<<'SQL'
            CREATE VIEW publish_view AS
            SELECT p.entity_id AS id,
                   p.published AS published,
                   CONCAT_WS(
                           ';',
                           cb.id,
                           CONCAT_WS(
                                   ',',
                                   cb_u.id,
                                   cb_u.username,
                                   COALESCE(cb_u.last_name, ''),
                                   COALESCE(cb_u.first_name, '')
                               ),
                           cb.created_at
                       )       AS created
            FROM (SELECT ROW_NUMBER() OVER (PARTITION BY entity_id ORDER BY id DESC) AS rownum, * FROM publish) p
                     JOIN created_by cb ON cb.id = p.id
                     JOIN users cb_u ON cb_u.id = cb.user_id
            WHERE p.rownum = 1
            SQL;
    }
}
