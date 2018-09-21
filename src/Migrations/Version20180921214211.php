<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180921214211 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE part_cross (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE part_cross_part (part_cross_id INT NOT NULL, part_id INT NOT NULL, INDEX IDX_B98F499C70B9088C (part_cross_id), UNIQUE INDEX UNIQ_B98F499C4CE34BEC (part_id), PRIMARY KEY(part_cross_id, part_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE part_cross_part ADD CONSTRAINT FK_B98F499C70B9088C FOREIGN KEY (part_cross_id) REFERENCES part_cross (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE part_cross_part ADD CONSTRAINT FK_B98F499C4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE part_cross_part DROP FOREIGN KEY FK_B98F499C70B9088C');
        $this->addSql('DROP TABLE part_cross');
        $this->addSql('DROP TABLE part_cross_part');
    }
}
