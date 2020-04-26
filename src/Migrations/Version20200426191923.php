<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function strpos;

final class Version20200426191923 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('DROP INDEX uniq_490f70c6d17f50a6');
        $this->addSql('ALTER TABLE part ADD part_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE part DROP uuid');
        $this->addSql('COMMENT ON COLUMN part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_490F70C64CE34BEC ON part (part_id)');
        $this->addSql('ALTER TABLE part ALTER part_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        throw new LogicException('No way.');
    }
}
