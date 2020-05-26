<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200526191244 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE part DROP price_amount');
        $this->addSql('ALTER TABLE part DROP price_currency_code');
        $this->addSql('ALTER TABLE part DROP discount_amount');
        $this->addSql('ALTER TABLE part DROP discount_currency_code');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE part ADD price_amount BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE part ADD price_currency_code VARCHAR(3) DEFAULT NULL');
        $this->addSql('ALTER TABLE part ADD discount_amount BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE part ADD discount_currency_code VARCHAR(3) DEFAULT NULL');
    }
}
