<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200310210614 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('DROP SEQUENCE appointment_id_seq CASCADE');
        $this->addSql('CREATE TABLE calendar_entry (
          id UUID NOT NULL, 
          worker_id INT DEFAULT NULL, 
          date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          duration VARCHAR(255) NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          description TEXT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_47759E1E6B20BA36 ON calendar_entry (worker_id)');
        $this->addSql('COMMENT ON COLUMN calendar_entry.id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.duration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE 
          calendar_entry 
        ADD 
          CONSTRAINT FK_47759E1E6B20BA36 FOREIGN KEY (worker_id) REFERENCES employee (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE appointment');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('CREATE SEQUENCE appointment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE appointment (
          id SERIAL NOT NULL, 
          order_id INT DEFAULT NULL, 
          date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          duration VARCHAR(255) NOT NULL, 
          created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          created_by_id INT DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_fe38f8448d9f6d38 ON appointment (order_id)');
        $this->addSql('COMMENT ON COLUMN appointment.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN appointment.duration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('COMMENT ON COLUMN appointment.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE 
          appointment 
        ADD 
          CONSTRAINT fk_fe38f8448d9f6d38 FOREIGN KEY (order_id) REFERENCES orders (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE calendar_entry');
    }
}
