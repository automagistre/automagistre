<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Appeal\Entity\AppealView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201230135643 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE appeal_call (id UUID NOT NULL, phone VARCHAR(35) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN appeal_call.id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_call.phone IS \'(DC2Type:phone_number)\'');

        $this->addSql('DROP VIEW IF EXISTS appeal_view');
        $this->addSql(AppealView::sql());
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE appeal_call');
    }
}
