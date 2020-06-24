<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200624124958 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE order_item_part ADD supplier_uuid UUID DEFAULT NULL');
        $this->addSql('UPDATE order_item_part SET supplier_uuid = sub.uuid FROM (SELECT id, uuid FROM operand) sub WHERE sub.id = order_item_part.supplier_id');
        $this->addSql('ALTER TABLE order_item_part DROP supplier_id');
        $this->addSql('ALTER TABLE order_item_part RENAME supplier_uuid TO supplier_id');
        $this->addSql('COMMENT ON COLUMN order_item_part.supplier_id IS \'(DC2Type:operand_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE order_item_part ALTER supplier_id TYPE INT');
        $this->addSql('ALTER TABLE order_item_part ALTER supplier_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN order_item_part.supplier_id IS NULL');
    }
}
