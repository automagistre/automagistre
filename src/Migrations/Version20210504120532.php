<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Appeal\Entity\AppealView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210504120532 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS appeal_view');

        $this->addSql('INSERT INTO created_by (id, user_id, created_at) SELECT id, \'00000000-0000-0000-0000-000000000000\', created_at FROM created_at');
        $this->addSql('DROP TABLE created_at');

        $this->addSql(AppealView::sql());
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE created_at (id UUID NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN created_at.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN created_at.created_at IS \'(DC2Type:datetimetz_immutable)\'');
    }
}
