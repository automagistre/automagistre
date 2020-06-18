<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200618152828 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE operand_transaction ADD uuid UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN operand_transaction.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE wallet_transaction ADD uuid UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE operand_transaction ALTER uuid SET NOT NULL');
        $this->addSql('ALTER TABLE wallet_transaction ALTER uuid SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE operand_transaction DROP uuid');
        $this->addSql('ALTER TABLE wallet_transaction DROP uuid');
    }
}
