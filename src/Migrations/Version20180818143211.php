<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180818143211 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE motion_order (id INT NOT NULL, order_id INT NOT NULL, INDEX IDX_1DF780628D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motion_reservation (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motion_income (id INT NOT NULL, income_part_id INT NOT NULL, INDEX IDX_6228A7C1F4A13D95 (income_part_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE motion_order ADD CONSTRAINT FK_1DF780628D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE motion_order ADD CONSTRAINT FK_1DF78062BF396750 FOREIGN KEY (id) REFERENCES motion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE motion_reservation ADD CONSTRAINT FK_B0FBD1CABF396750 FOREIGN KEY (id) REFERENCES motion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE motion_income ADD CONSTRAINT FK_6228A7C1F4A13D95 FOREIGN KEY (income_part_id) REFERENCES income_part (id)');
        $this->addSql('ALTER TABLE motion_income ADD CONSTRAINT FK_6228A7C1BF396750 FOREIGN KEY (id) REFERENCES motion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE motion DROP FOREIGN KEY FK_F5FEA1E88D9F6D38');
        $this->addSql('DROP INDEX IDX_F5FEA1E88D9F6D38 ON motion');
        $this->addSql('ALTER TABLE motion ADD type INT NOT NULL');
        $this->addSql('INSERT INTO motion_order (id, order_id) SELECT id, order_id FROM motion WHERE order_id IS NOT NULL');
        $this->addSql('UPDATE motion SET type = 1 WHERE order_id IS NOT NULL');
        $this->addSql('ALTER TABLE motion DROP order_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE motion_order');
        $this->addSql('DROP TABLE motion_reservation');
        $this->addSql('DROP TABLE motion_income');
        $this->addSql('ALTER TABLE motion ADD order_id INT DEFAULT NULL, DROP type');
        $this->addSql('ALTER TABLE motion ADD CONSTRAINT FK_F5FEA1E88D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_F5FEA1E88D9F6D38 ON motion (order_id)');
    }
}
