<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200506162703 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('DROP SEQUENCE part_case_id_seq CASCADE');
        $this->addSql('ALTER TABLE part_case DROP id');
        $this->addSql('ALTER TABLE part_case ADD PRIMARY KEY (part_id, vehicle_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('CREATE SEQUENCE part_case_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP INDEX idx_16548_primary');
        $this->addSql('ALTER TABLE part_case ADD id SERIAL NOT NULL');
        $this->addSql('ALTER TABLE part_case ADD PRIMARY KEY (id)');
    }
}
