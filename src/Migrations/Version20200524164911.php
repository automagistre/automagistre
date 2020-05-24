<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function strpos;

final class Version20200524164911 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE stockpile ADD part_uuid UUID DEFAULT NULL');
        $this->addSql('DROP SEQUENCE stockpile_id_seq CASCADE');
        $this->addSql('ALTER TABLE stockpile DROP id');
        $this->addSql('ALTER TABLE stockpile DROP part_id');
        $this->addSql('ALTER TABLE stockpile RENAME part_uuid TO part_id');
        $this->addSql('ALTER TABLE stockpile ALTER part_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN stockpile.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('ALTER TABLE stockpile ADD PRIMARY KEY (part_id, tenant)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        throw new LogicException('Nope');
    }
}
