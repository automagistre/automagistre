<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200625190605 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE orders DROP CONSTRAINT fk_e52ffdeeb03a8386');
        $this->addSql('DROP INDEX idx_e52ffdeeb03a8386');
        // data migration
        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at)
            SELECT o.uuid, u.uuid, o.created_at
            FROM orders o
                JOIN users u ON u.id = o.created_by_id
            ON CONFLICT DO NOTHING
        ');
        // data migration
        $this->addSql('ALTER TABLE orders DROP created_by_id');
        $this->addSql('ALTER TABLE orders DROP created_at');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE orders ADD created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE orders ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN orders.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT fk_e52ffdeeb03a8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_e52ffdeeb03a8386 ON orders (created_by_id)');
    }
}
