<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170325203513 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE cache_price');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE keys_price');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D217BBB47 FOREIGN KEY (person_id) REFERENCES operand (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cache_price (id INT AUTO_INCREMENT NOT NULL, price DOUBLE PRECISION DEFAULT NULL, term TINYINT(1) DEFAULT NULL, man_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, pn VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, part_name TEXT DEFAULT NULL COLLATE utf8_unicode_ci, id_price INT DEFAULT NULL, id_d2m INT DEFAULT NULL, qty INT DEFAULT NULL, prc_ok TINYINT(1) DEFAULT NULL, dir_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, min_qty INT DEFAULT NULL, type_cross TINYINT(1) DEFAULT NULL, qid INT DEFAULT NULL, grp_part TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, wallet INT NOT NULL, description TEXT DEFAULT NULL COLLATE utf8_unicode_ci, employee TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_C7440455217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE keys_price (id INT AUTO_INCREMENT NOT NULL, query VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, source VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, last DATETIME DEFAULT NULL, cnt TINYINT(1) DEFAULT NULL, man VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1217BBB47');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D217BBB47');
    }
}
