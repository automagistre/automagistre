<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200523174011 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE motion ADD part_uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE motion DROP part_id');
        $this->addSql('ALTER TABLE motion RENAME part_uuid TO part_id');
        $this->addSql('ALTER TABLE motion ALTER part_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN motion.part_id IS \'(DC2Type:part_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE motion ALTER part_id TYPE INT');
        $this->addSql('ALTER TABLE motion ALTER part_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN motion.part_id IS NULL');
    }
}
