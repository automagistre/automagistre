<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181011190849 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE income_part ADD accrued_motion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE income_part ADD CONSTRAINT FK_834566E8FFE2C7 FOREIGN KEY (accrued_motion_id) REFERENCES motion_income (id)');
        $this->addSql('CREATE INDEX IDX_834566E8FFE2C7 ON income_part (accrued_motion_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE income_part DROP FOREIGN KEY FK_834566E8FFE2C7');
        $this->addSql('DROP INDEX IDX_834566E8FFE2C7 ON income_part');
        $this->addSql('ALTER TABLE income_part DROP accrued_motion_id');
    }
}
