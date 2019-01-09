<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190109224328 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('tenant' !== $this->connection->getDatabase(), 'Tenant only');

        $this->addSql('ALTER TABLE wallet_transaction ADD created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE operand_transaction ADD created_by_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');

        $this->addSql('UPDATE wallet_transaction SET created_by_uuid = 0xA99DFF32144A11E9AC6602420A000329 WHERE created_by_uuid IS NULL');
        $this->addSql('UPDATE operand_transaction SET created_by_uuid = 0xA99DFF32144A11E9AC6602420A000329 WHERE created_by_uuid IS NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operand_transaction DROP created_by_uuid');
        $this->addSql('ALTER TABLE wallet_transaction DROP created_by_uuid');
    }
}
