<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200531132044 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('CREATE TABLE calendar_entry_order (id UUID NOT NULL, entry_id UUID DEFAULT NULL, customer_id UUID DEFAULT NULL, car_id UUID DEFAULT NULL, description TEXT DEFAULT NULL, worker_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1CF4E75BA364942 ON calendar_entry_order (entry_id)');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.customer_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.car_id IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.worker_id IS \'(DC2Type:employee_id)\'');
        $this->addSql('CREATE TABLE calendar_entry_schedule (id UUID NOT NULL, entry_id UUID DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, duration VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_86FDAEE3BA364942 ON calendar_entry_schedule (entry_id)');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.duration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE calendar_entry_order ADD CONSTRAINT FK_1CF4E75BA364942 FOREIGN KEY (entry_id) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE calendar_entry_schedule ADD CONSTRAINT FK_86FDAEE3BA364942 FOREIGN KEY (entry_id) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE calendar_entry DROP CONSTRAINT fk_47759e1e2cf9ddc');
        $this->addSql('DROP INDEX uniq_47759e1e2cf9ddc');
        $this->addSql('ALTER TABLE calendar_entry DROP previous');
        $this->addSql('ALTER TABLE calendar_entry DROP date');
        $this->addSql('ALTER TABLE calendar_entry DROP duration');
        $this->addSql('ALTER TABLE calendar_entry DROP description');
        $this->addSql('ALTER TABLE calendar_entry DROP car_id');
        $this->addSql('ALTER TABLE calendar_entry DROP customer_id');
        $this->addSql('ALTER TABLE calendar_entry DROP worker_id');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('DROP TABLE calendar_entry_order');
        $this->addSql('DROP TABLE calendar_entry_schedule');
        $this->addSql('ALTER TABLE calendar_entry ADD previous UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD duration VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD car_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD customer_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD worker_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN calendar_entry.previous IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.duration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.car_id IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.customer_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.worker_id IS \'(DC2Type:employee_id)\'');
        $this->addSql('ALTER TABLE calendar_entry ADD CONSTRAINT fk_47759e1e2cf9ddc FOREIGN KEY (previous) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_47759e1e2cf9ddc ON calendar_entry (previous)');
    }
}
