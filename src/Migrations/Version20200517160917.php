<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200517160917 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE mc_work ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE mc_work ALTER price_amount TYPE BIGINT USING price_amount::bigint');
        $this->addSql('ALTER TABLE part ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE part ALTER price_amount TYPE BIGINT USING price_amount::bigint');
        $this->addSql('ALTER TABLE part ALTER discount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE part ALTER discount_amount TYPE BIGINT USING discount_amount::bigint');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE mc_work ALTER price_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE mc_work ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE part ALTER price_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE part ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE part ALTER discount_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE part ALTER discount_amount DROP DEFAULT');
    }
}
