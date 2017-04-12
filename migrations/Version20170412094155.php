<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170412094155 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET SESSION SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"');
        $this->addSql('INSERT INTO operand (id, type) VALUES (0, 1)');
        $this->addSql('INSERT INTO person (id, firstname) VALUES (0, \'неустановленный\')');

        $this->addSql('ALTER TABLE order_part ADD selector_id INT NOT NULL , ADD created_at DATETIME NOT NULL');
        $this->addSql('UPDATE order_part SET selector_id = 0 WHERE selector_id IS NULL');
        $this->addSql('UPDATE order_part SET created_at = NOW()');
        $this->addSql('ALTER TABLE order_part ADD CONSTRAINT FK_4FE4AD1706C1B43 FOREIGN KEY (selector_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_4FE4AD1706C1B43 ON order_part (selector_id)');

        $this->addSql('ALTER TABLE car_recommendation_part ADD selector_id INT NOT NULL, ADD created_at DATETIME NOT NULL');
        $this->addSql('UPDATE car_recommendation_part SET selector_id = 0 WHERE selector_id IS NULL');
        $this->addSql('UPDATE car_recommendation_part SET created_at = NOW()');
        $this->addSql('ALTER TABLE car_recommendation_part ADD CONSTRAINT FK_DDC72D65706C1B43 FOREIGN KEY (selector_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_DDC72D65706C1B43 ON car_recommendation_part (selector_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_recommendation_part DROP FOREIGN KEY FK_DDC72D65706C1B43');
        $this->addSql('DROP INDEX IDX_DDC72D65706C1B43 ON car_recommendation_part');
        $this->addSql('ALTER TABLE car_recommendation_part DROP selector_id, DROP created_at');
        $this->addSql('ALTER TABLE order_part DROP FOREIGN KEY FK_4FE4AD1706C1B43');
        $this->addSql('DROP INDEX IDX_4FE4AD1706C1B43 ON order_part');
        $this->addSql('ALTER TABLE order_part DROP selector_id, DROP created_at');

        $this->addSql('DELETE person FROM person WHERE id = 0');
        $this->addSql('DELETE operand FROM operand WHERE id = 0');
    }
}
