<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function strpos;

final class Version20200524183137 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('DROP TABLE part_part');
        $this->addSql('DROP INDEX uniq_490f70c64ce34bec');

        $this->addSql('ALTER TABLE part_cross_part ADD part_uuid UUID DEFAULT NULL');
        // Data migration
        $this->addSql('UPDATE part_cross_part SET part_uuid = b.uuid FROM (SELECT id, part_id AS uuid FROM part) b WHERE b.id = part_id');
        // Data migration
        $this->addSql('ALTER TABLE part_cross_part DROP part_id');
        $this->addSql('ALTER TABLE part_cross_part RENAME part_uuid TO part_id');
        $this->addSql('ALTER TABLE part_cross_part ALTER part_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN part_cross_part.part_id IS \'(DC2Type:part_id)\'');

        $this->addSql('ALTER TABLE part DROP id');
        $this->addSql('ALTER TABLE part RENAME part_id TO id');
        $this->addSql('ALTER TABLE part ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE part_cross_part ADD CONSTRAINT FK_B98F499C4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B98F499C4CE34BEC ON part_cross_part (part_id)');
        $this->addSql('ALTER TABLE part_cross_part ADD PRIMARY KEY (part_cross_id, part_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        throw new LogicException('Nope');
    }
}
