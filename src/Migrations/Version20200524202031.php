<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200524202031 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE part_case ADD id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN part_case.id IS \'(DC2Type:part_case_id)\'');
        $this->addSql('ALTER TABLE part_case ALTER id SET NOT NULL');
        $this->addSql('ALTER TABLE part_case DROP CONSTRAINT part_case_pkey');
        $this->addSql('ALTER TABLE part_case ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('DROP INDEX part_case_pkey');
        $this->addSql('ALTER TABLE part_case DROP id');
        $this->addSql('ALTER TABLE part_case ADD PRIMARY KEY (part_id, vehicle_id)');
    }
}
