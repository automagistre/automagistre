<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200526155927 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('CREATE TABLE part_price (
          id UUID NOT NULL, 
          part_id UUID NOT NULL, 
          since TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          price_amount BIGINT DEFAULT NULL, 
          price_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN part_price.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_price.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_price.since IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('CREATE TABLE part_discount (
          id UUID NOT NULL, 
          part_id UUID NOT NULL, 
          since TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          discount_amount BIGINT DEFAULT NULL, 
          discount_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN part_discount.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_discount.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_discount.since IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('DROP TABLE part_price');
        $this->addSql('DROP TABLE part_discount');
    }
}
