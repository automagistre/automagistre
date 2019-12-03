<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191203124250 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('UPDATE motion SET part_id = 16701 WHERE part_id IS NULL');
        $this->addSql('UPDATE order_item_part SET part_id = 16701 WHERE part_id IS NULL');

        $this->addSql('ALTER TABLE motion CHANGE part_id part_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_item_part CHANGE part_id part_id INT NOT NULL');
        $this->addSql('ALTER TABLE income_part CHANGE part_id part_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'Tenant only');

        $this->addSql('ALTER TABLE income_part CHANGE part_id part_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE motion CHANGE part_id part_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item_part CHANGE part_id part_id INT DEFAULT NULL');
    }
}
