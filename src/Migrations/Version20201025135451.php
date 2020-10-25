<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201025135451 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE order_feedback (id UUID NOT NULL, order_id UUID NOT NULL, satisfaction SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN order_feedback.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_feedback.order_id IS \'(DC2Type:order_id)\'');
        $this->addSql('COMMENT ON COLUMN order_feedback.satisfaction IS \'(DC2Type:order_satisfaction_enum)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE order_feedback');
    }
}
