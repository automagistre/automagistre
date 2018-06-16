<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170423130536 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE income_part (id INT AUTO_INCREMENT NOT NULL, income_id INT DEFAULT NULL, part_id INT DEFAULT NULL, price INT NOT NULL, currency VARCHAR(3) NOT NULL, quantity INT NOT NULL, INDEX IDX_834566E8640ED2C0 (income_id), INDEX IDX_834566E84CE34BEC (part_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE income (id INT AUTO_INCREMENT NOT NULL, supplier_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_3FA862D02ADD6D8C (supplier_id), INDEX IDX_3FA862D0B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE income_part ADD CONSTRAINT FK_834566E8640ED2C0 FOREIGN KEY (income_id) REFERENCES income (id)');
        $this->addSql('ALTER TABLE income_part ADD CONSTRAINT FK_834566E84CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE income ADD CONSTRAINT FK_3FA862D02ADD6D8C FOREIGN KEY (supplier_id) REFERENCES operand (id)');
        $this->addSql('ALTER TABLE income ADD CONSTRAINT FK_3FA862D0B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE income_part DROP FOREIGN KEY FK_834566E8640ED2C0');
        $this->addSql('DROP TABLE income_part');
        $this->addSql('DROP TABLE income');
    }
}
