<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200601105340 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE calendar_entry_order RENAME TO calendar_entry_order_info');
        $this->addSql('CREATE TABLE calendar_entry_order (id UUID NOT NULL, entry_id UUID NOT NULL, order_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.order_id IS \'(DC2Type:order_id)\'');
        $this->addSql('ALTER INDEX idx_1cf4e75ba364942 RENAME TO IDX_5FBDE1C1BA364942');
        $this->addSql('ALTER TABLE orders DROP appointment_at');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('DROP TABLE calendar_entry_order');
        $this->addSql('ALTER TABLE orders ADD appointment_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN orders.appointment_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER INDEX idx_5fbde1c1ba364942 RENAME TO idx_1cf4e75ba364942');
        $this->addSql('ALTER TABLE calendar_entry_order_info RENAME TO calendar_entry_order');
    }
}
