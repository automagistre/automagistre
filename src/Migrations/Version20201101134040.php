<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201101134040 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE order_cancel (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN order_cancel.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE order_deal (id UUID NOT NULL, balance VARCHAR(255) DEFAULT NULL, satisfaction SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE order_close ADD type VARCHAR(255) DEFAULT NULL');

        //> DataMigration
        $this->addSql('UPDATE order_close SET type = 1 WHERE type IS NULL');
        $this->addSql('INSERT INTO order_deal SELECT id, balance, satisfaction FROM order_close');
        //< DataMigration

        $this->addSql('ALTER TABLE order_close ALTER type SET NOT NULL');

        $this->addSql('COMMENT ON COLUMN order_deal.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_deal.balance IS \'(DC2Type:money)\'');
        $this->addSql('COMMENT ON COLUMN order_deal.satisfaction IS \'(DC2Type:order_satisfaction_enum)\'');
        $this->addSql('ALTER TABLE order_cancel ADD CONSTRAINT FK_9599D5A7BF396750 FOREIGN KEY (id) REFERENCES order_close (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_deal ADD CONSTRAINT FK_AE0FFB01BF396750 FOREIGN KEY (id) REFERENCES order_close (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE order_close DROP balance');
        $this->addSql('ALTER TABLE order_close DROP satisfaction');
    }
}
