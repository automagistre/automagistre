<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201123123708 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mc_line ALTER work_id TYPE UUID');
        $this->addSql('ALTER TABLE mc_line ALTER work_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN mc_line.work_id IS \'(DC2Type:mc_work_id)\'');
        $this->addSql('ALTER TABLE mc_work ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE mc_work ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN mc_work.id IS \'(DC2Type:mc_work_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mc_work ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE mc_work ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN mc_work.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE mc_line ALTER work_id TYPE UUID');
        $this->addSql('ALTER TABLE mc_line ALTER work_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN mc_line.work_id IS \'(DC2Type:uuid)\'');
    }
}
