<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190222113013 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('tenant' !== $this->connection->getDatabase(), 'Tenant only');

        $this->addSql('ALTER TABLE salary DROP FOREIGN KEY FK_9413BB712FC0CB0F');
        $this->addSql('DROP INDEX IDX_9413BB712FC0CB0F ON salary');
        $this->addSql('ALTER TABLE salary ADD outcome_id INT DEFAULT NULL, CHANGE transaction_id income_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE salary ADD CONSTRAINT FK_9413BB71640ED2C0 FOREIGN KEY (income_id) REFERENCES operand_transaction (id)');
        $this->addSql('ALTER TABLE salary ADD CONSTRAINT FK_9413BB71E6EE6D63 FOREIGN KEY (outcome_id) REFERENCES wallet_transaction (id)');
        $this->addSql('CREATE INDEX IDX_9413BB71640ED2C0 ON salary (income_id)');
        $this->addSql('CREATE INDEX IDX_9413BB71E6EE6D63 ON salary (outcome_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('tenant' !== $this->connection->getDatabase(), 'Tenant only');

        $this->addSql('ALTER TABLE salary DROP FOREIGN KEY FK_9413BB71640ED2C0');
        $this->addSql('ALTER TABLE salary DROP FOREIGN KEY FK_9413BB71E6EE6D63');
        $this->addSql('DROP INDEX IDX_9413BB71640ED2C0 ON salary');
        $this->addSql('DROP INDEX IDX_9413BB71E6EE6D63 ON salary');
        $this->addSql('ALTER TABLE salary ADD transaction_id INT DEFAULT NULL, DROP income_id, DROP outcome_id');
        $this->addSql('ALTER TABLE salary ADD CONSTRAINT FK_9413BB712FC0CB0F FOREIGN KEY (transaction_id) REFERENCES operand_transaction (id)');
        $this->addSql('CREATE INDEX IDX_9413BB712FC0CB0F ON salary (transaction_id)');
    }
}
