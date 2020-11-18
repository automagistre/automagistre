<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201118095925 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE order_number CASCADE');
        $this->addSql('ALTER TABLE appeal_schedule ADD phone VARCHAR(35) NOT NULL');
        $this->addSql('ALTER TABLE appeal_schedule DROP email');
        $this->addSql('COMMENT ON COLUMN appeal_schedule.phone IS \'(DC2Type:phone_number)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE order_number INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE appeal_schedule ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE appeal_schedule DROP phone');
    }
}
