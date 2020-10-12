<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201011194114 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE order_close (id UUID NOT NULL, order_id UUID DEFAULT NULL, balance VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_909FF5398D9F6D38 ON order_close (order_id)');
        $this->addSql('COMMENT ON COLUMN order_close.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_close.order_id IS \'(DC2Type:order_id)\'');
        $this->addSql('COMMENT ON COLUMN order_close.balance IS \'(DC2Type:money)\'');
        $this->addSql('ALTER TABLE order_close ADD CONSTRAINT FK_909FF5398D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE orders DROP CONSTRAINT fk_e52ffdeee1fa7797');
        $this->addSql('DROP INDEX idx_e52ffdee5d13417f');
        $this->addSql('DROP INDEX idx_e52ffdeee1fa7797');
        $this->addSql('ALTER TABLE orders DROP closed_by_id');
        $this->addSql('ALTER TABLE orders DROP closed_at');
        $this->addSql('ALTER TABLE orders DROP closed_balance_amount');
        $this->addSql('ALTER TABLE orders DROP closed_balance_currency_code');
    }
}
