<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200518112411 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE calendar_entry DROP CONSTRAINT fk_47759e1e6b20ba36');
        $this->addSql('DROP INDEX idx_47759e1e6b20ba36');
        $this->addSql('ALTER TABLE calendar_entry ADD customer_id UUID DEFAULT NULL');
        $this->addSql('UPDATE calendar_entry SET description = concat_ws(\' \', first_name, last_name, phone, description)');
        $this->addSql('ALTER TABLE calendar_entry DROP first_name');
        $this->addSql('ALTER TABLE calendar_entry DROP last_name');
        $this->addSql('ALTER TABLE calendar_entry DROP phone');
        $this->addSql('ALTER TABLE calendar_entry ADD worker_uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry DROP worker_id');
        $this->addSql('ALTER TABLE calendar_entry RENAME worker_uuid TO worker_id');

        $this->addSql('COMMENT ON COLUMN calendar_entry.customer_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.worker_id IS \'(DC2Type:employee_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE calendar_entry ADD first_name VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD last_name VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD phone VARCHAR(35) DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry DROP customer_id');
        $this->addSql('ALTER TABLE calendar_entry ALTER worker_id TYPE INT');
        $this->addSql('ALTER TABLE calendar_entry ALTER worker_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN calendar_entry.phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.worker_id IS NULL');
        $this->addSql('ALTER TABLE 
          calendar_entry 
        ADD 
          CONSTRAINT fk_47759e1e6b20ba36 FOREIGN KEY (worker_id) REFERENCES employee (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_47759e1e6b20ba36 ON calendar_entry (worker_id)');
    }
}
