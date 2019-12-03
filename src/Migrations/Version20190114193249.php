<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190114193249 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'Landlord only');

        $this->addSql('DROP INDEX UNIQ_773DE69DD17F50A6 ON car');
        $this->addSql('ALTER TABLE car DROP uuid');
        $this->addSql('DROP INDEX UNIQ_1483A5E9D17F50A6 ON users');
        $this->addSql('ALTER TABLE users DROP uuid');
        $this->addSql('DROP INDEX UNIQ_83E03CE6D17F50A6 ON operand');
        $this->addSql('ALTER TABLE operand DROP uuid');
        $this->addSql('ALTER TABLE car_recommendation DROP realization_uuid');
        $this->addSql('DROP INDEX UNIQ_490F70C6D17F50A6 ON part');
        $this->addSql('ALTER TABLE part DROP uuid');
        $this->addSql('DROP INDEX UNIQ_3D0AE6DCD17F50A6 ON manufacturer');
        $this->addSql('ALTER TABLE manufacturer DROP uuid');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'Landlord only');

        $this->addSql('ALTER TABLE car ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69DD17F50A6 ON car (uuid)');
        $this->addSql('ALTER TABLE car_recommendation ADD realization_uuid BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('ALTER TABLE manufacturer ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3D0AE6DCD17F50A6 ON manufacturer (uuid)');
        $this->addSql('ALTER TABLE operand ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83E03CE6D17F50A6 ON operand (uuid)');
        $this->addSql('ALTER TABLE part ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_490F70C6D17F50A6 ON part (uuid)');
        $this->addSql('ALTER TABLE users ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9D17F50A6 ON users (uuid)');
    }
}
