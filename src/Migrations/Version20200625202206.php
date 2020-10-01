<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200625202206 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE mc_work ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE mc_line ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE mc_part ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE mc_work ALTER uuid SET NOT NULL');
        $this->addSql('ALTER TABLE mc_line ALTER uuid SET NOT NULL');
        $this->addSql('ALTER TABLE mc_part ALTER uuid SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN mc_work.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mc_line.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mc_part.uuid IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE mc_work DROP uuid');
        $this->addSql('ALTER TABLE mc_line DROP uuid');
        $this->addSql('ALTER TABLE mc_part DROP uuid');
    }
}
