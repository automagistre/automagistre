<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;
use function array_map;
use function explode;
use function implode;
use function is_numeric;
use function mb_strtoupper;
use function mb_substr;
use function trim;

final class Version20210809112150 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE warehouse_code (
          id UUID NOT NULL,
          warehouse_id UUID NOT NULL,
          code VARCHAR(255) NOT NULL,
          PRIMARY KEY(id)
        )');

        //>DataMigration
        $warehouses = $this->connection->fetchAllAssociative(
            <<<'SQL'
            WITH RECURSIVE tree (id) AS (
                SELECT id, wp.warehouse_parent_id AS parent_id, 0 AS depth
                FROM warehouse root
                         LEFT JOIN LATERAL (SELECT warehouse_parent_id
                                            FROM warehouse_parent sub
                                            WHERE sub.warehouse_id = root.id
                                            ORDER BY sub.id DESC
                                            LIMIT 1
                    ) wp ON TRUE
                WHERE wp.warehouse_parent_id IS NULL

                UNION ALL

                SELECT root.id, wp.warehouse_parent_id AS parent_id, p.depth + 1 AS depth
                FROM warehouse root
                         LEFT JOIN LATERAL (SELECT warehouse_parent_id
                                            FROM warehouse_parent sub
                                            WHERE sub.warehouse_id = root.id
                                            ORDER BY sub.id DESC
                                            LIMIT 1
                    ) wp ON TRUE
                         JOIN tree p ON p.id = wp.warehouse_parent_id
            )
            SELECT tree.id        AS id,
                   wn.name        AS NAME,
                   tree.parent_id AS parent_id,
                   tree.depth     AS depth
            FROM tree
                     JOIN LATERAL (SELECT NAME
                                   FROM warehouse_name sub
                                   WHERE sub.warehouse_id = tree.id
                                   ORDER BY sub.id DESC
                                   LIMIT 1
                ) wn ON TRUE
            SQL,
        );
        foreach ($warehouses as $item) {
            $this->addSql('INSERT INTO warehouse_code (id, warehouse_id, code) VALUES (:id, :warehouse_id, :code)', [
                'id' => Uuid::uuid6()->toString(),
                'warehouse_id' => $item['id'],
                'code' => implode(
                    '',
                    array_map(
                        static fn (string $name) => is_numeric($name) ? $name : trim(mb_strtoupper(mb_substr($name, 0, 1))),
                        explode(' ', $item['name']),
                    ),
                ),
            ]);
        }
        //<DataMigration

        $this->addSql('COMMENT ON COLUMN warehouse_code.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_code.warehouse_id IS \'(DC2Type:warehouse_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE warehouse_code');
    }
}
