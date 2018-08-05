<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180805203645 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE income_part ADD supply_id INT DEFAULT NULL, DROP currency');
        $this->addSql('ALTER TABLE income_part ADD CONSTRAINT FK_834566E8FF28C0D8 FOREIGN KEY (supply_id) REFERENCES supply (id)');
        $this->addSql('CREATE INDEX IDX_834566E8FF28C0D8 ON income_part (supply_id)');
        $this->addSql('ALTER TABLE income ADD accrued_by_id INT DEFAULT NULL, ADD document VARCHAR(255) DEFAULT NULL, ADD accrued_at DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE income ADD CONSTRAINT FK_3FA862D0748C73B5 FOREIGN KEY (accrued_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_3FA862D0748C73B5 ON income (accrued_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE income DROP FOREIGN KEY FK_3FA862D0748C73B5');
        $this->addSql('DROP INDEX IDX_3FA862D0748C73B5 ON income');
        $this->addSql('ALTER TABLE income DROP accrued_by_id, DROP document, DROP accrued_at');
        $this->addSql('ALTER TABLE income_part DROP FOREIGN KEY FK_834566E8FF28C0D8');
        $this->addSql('DROP INDEX IDX_834566E8FF28C0D8 ON income_part');
        $this->addSql('ALTER TABLE income_part ADD currency VARCHAR(3) NOT NULL COLLATE utf8_unicode_ci, DROP supply_id');
    }
}
