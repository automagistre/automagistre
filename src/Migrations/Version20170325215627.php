<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170325215627 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D217BBB47');
        $this->addSql('DROP INDEX IDX_6D28840D217BBB47 ON payment');
        $this->addSql('ALTER TABLE payment CHANGE person_id recipient_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DE92F8F78 FOREIGN KEY (recipient_id) REFERENCES operand (id)');
        $this->addSql('CREATE INDEX IDX_6D28840DE92F8F78 ON payment (recipient_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DE92F8F78');
        $this->addSql('DROP INDEX IDX_6D28840DE92F8F78 ON payment');
        $this->addSql('ALTER TABLE payment CHANGE recipient_id person_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D217BBB47 FOREIGN KEY (person_id) REFERENCES operand (id)');
        $this->addSql('CREATE INDEX IDX_6D28840D217BBB47 ON payment (person_id)');
    }
}
