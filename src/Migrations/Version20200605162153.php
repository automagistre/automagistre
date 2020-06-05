<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200605162153 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('CREATE TABLE warehouse (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN warehouse.id IS \'(DC2Type:warehouse_id)\'');
        $this->addSql('CREATE TABLE warehouse_name (id UUID NOT NULL, warehouse_id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN warehouse_name.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_name.warehouse_id IS \'(DC2Type:warehouse_id)\'');
        $this->addSql('CREATE TABLE warehouse_parent (id UUID NOT NULL, warehouse_id UUID NOT NULL, warehouse_parent_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN warehouse_parent.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_parent.warehouse_id IS \'(DC2Type:warehouse_id)\'');
        $this->addSql('COMMENT ON COLUMN warehouse_parent.warehouse_parent_id IS \'(DC2Type:warehouse_id)\'');

        $this->addSql('
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
        ');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('DROP TABLE warehouse');
        $this->addSql('DROP TABLE warehouse_name');
        $this->addSql('DROP TABLE warehouse_parent');
    }
}
