<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210912121655 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE created_by DROP tenant_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE created_by ADD tenant_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN created_by.tenant_id IS \'(DC2Type:tenant_id)\'');
    }
}
