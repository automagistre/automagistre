<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201011223225 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE order_contractor');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE order_contractor (id UUID NOT NULL, money_amount BIGINT DEFAULT NULL, money_currency_code VARCHAR(3) DEFAULT NULL, order_id UUID NOT NULL, operand_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN order_contractor.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_contractor.order_id IS \'(DC2Type:order_id)\'');
        $this->addSql('COMMENT ON COLUMN order_contractor.operand_id IS \'(DC2Type:operand_id)\'');
    }
}
