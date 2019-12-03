<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190201135314 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'Landlord only');

        $this->addSql('CREATE TABLE part_part (part_source INT NOT NULL, part_target INT NOT NULL, INDEX IDX_33A70E4B661ABFE6 (part_source), INDEX IDX_33A70E4B7FFFEF69 (part_target), PRIMARY KEY(part_source, part_target)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE part_part ADD CONSTRAINT FK_33A70E4B661ABFE6 FOREIGN KEY (part_source) REFERENCES part (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE part_part ADD CONSTRAINT FK_33A70E4B7FFFEF69 FOREIGN KEY (part_target) REFERENCES part (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE part_part');
    }
}
