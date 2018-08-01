<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180801141155 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('RENAME TABLE note TO order_note');
        $this->addSql('ALTER TABLE order_note ADD type SMALLINT NOT NULL COMMENT \'(DC2Type:note_type_enum)\', CHANGE description text LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE order_note DROP FOREIGN KEY FK_CFBDFA148D9F6D38');
        $this->addSql('DROP INDEX idx_cfbdfa148d9f6d38 ON order_note');
        $this->addSql('CREATE INDEX IDX_824CC0038D9F6D38 ON order_note (order_id)');
        $this->addSql('ALTER TABLE order_note ADD CONSTRAINT FK_CFBDFA148D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');

        $this->addSql('CREATE TABLE car_note (id INT AUTO_INCREMENT NOT NULL, car_id INT DEFAULT NULL, type SMALLINT NOT NULL COMMENT \'(DC2Type:note_type_enum)\', text LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4D7EEB8C3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operand_note (id INT AUTO_INCREMENT NOT NULL, operand_id INT DEFAULT NULL, type SMALLINT NOT NULL COMMENT \'(DC2Type:note_type_enum)\', text LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_36BDE44118D7F226 (operand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE car_note ADD CONSTRAINT FK_4D7EEB8C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE operand_note ADD CONSTRAINT FK_36BDE44118D7F226 FOREIGN KEY (operand_id) REFERENCES operand (id)');

        $this->addSql('ALTER TABLE car_note ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car_note ADD CONSTRAINT FK_4D7EEB8B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_4D7EEB8B03A8386 ON car_note (created_by_id)');
        $this->addSql('ALTER TABLE order_note ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_note ADD CONSTRAINT FK_824CC003B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_824CC003B03A8386 ON order_note (created_by_id)');
        $this->addSql('ALTER TABLE operand_note ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE operand_note ADD CONSTRAINT FK_36BDE441B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_36BDE441B03A8386 ON operand_note (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }
}
