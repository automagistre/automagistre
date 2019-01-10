<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190110123905 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('landlord' !== $this->connection->getDatabase(), 'Landlord only');

        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA79033212A');
        $this->addSql('DROP TABLE tenant');
        $this->addSql('ALTER TABLE car_recommendation CHANGE realization_tenant realization_tenant SMALLINT NOT NULL COMMENT \'(DC2Type:tenant_enum)\'');
        $this->addSql('DROP INDEX IDX_3BAE0AA79033212A ON event');
        $this->addSql('ALTER TABLE event CHANGE tenant_id tenant SMALLINT NULL COMMENT \'(DC2Type:tenant_enum)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tenant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, identifier VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_4E59C462772E836A (identifier), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE car_recommendation CHANGE realization_tenant realization_tenant INT NOT NULL');
        $this->addSql('ALTER TABLE event ADD tenant_id INT DEFAULT NULL, DROP tenant');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA79033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA79033212A ON event (tenant_id)');
    }
}
