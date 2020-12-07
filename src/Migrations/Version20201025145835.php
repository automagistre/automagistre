<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201025145835 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_close ADD satisfaction SMALLINT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN order_close.satisfaction IS \'(DC2Type:order_satisfaction_enum)\'');

        //> DataMigration
        $this->addSql('
            UPDATE order_close c
            SET satisfaction = f.satisfaction
            FROM order_feedback f
            WHERE f.order_id = c.order_id
            ');
        $this->addSql('UPDATE order_close SET satisfaction = 0 WHERE satisfaction IS NULL');
        //< DataMigration

        $this->addSql('ALTER TABLE order_close ALTER satisfaction SET NOT NULL');
        $this->addSql('DROP TABLE order_feedback');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE order_feedback (id UUID NOT NULL, order_id UUID NOT NULL, satisfaction SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN order_feedback.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_feedback.order_id IS \'(DC2Type:order_id)\'');
        $this->addSql('COMMENT ON COLUMN order_feedback.satisfaction IS \'(DC2Type:order_satisfaction_enum)\'');
        $this->addSql('ALTER TABLE order_close DROP satisfaction');
    }
}
