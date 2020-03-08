<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200308133640 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'Landlord only');

        $this->addSql('ALTER TABLE operand ADD uuid UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN operand.uuid IS \'(DC2Type:operand_id)\'');
        $this->addSql('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');
        $this->addSql('UPDATE operand SET uuid = uuid_generate_v4() WHERE uuid IS NULL');
        $this->addSql('DROP EXTENSION "uuid-ossp"');
        $this->addSql('ALTER TABLE operand ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE operand ALTER uuid DROP DEFAULT');
        $this->addSql('ALTER TABLE operand ALTER uuid SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83E03CE6D17F50A6 ON operand (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'Landlord only');

        $this->addSql('ALTER TABLE operand DROP uuid');
    }
}
