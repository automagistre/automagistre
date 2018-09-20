<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180917093532 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09B03A8386 ON order_item (created_by_id)');
        $this->addSql('ALTER TABLE orders ADD closed_by_id INT DEFAULT NULL, ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEE1FA7797 FOREIGN KEY (closed_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEE1FA7797 ON orders (closed_by_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEB03A8386 ON orders (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09B03A8386');
        $this->addSql('DROP INDEX IDX_52EA1F09B03A8386 ON order_item');
        $this->addSql('ALTER TABLE order_item DROP created_by_id');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEE1FA7797');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEB03A8386');
        $this->addSql('DROP INDEX IDX_E52FFDEEE1FA7797 ON orders');
        $this->addSql('DROP INDEX IDX_E52FFDEEB03A8386 ON orders');
        $this->addSql('ALTER TABLE orders DROP closed_by_id, DROP created_by_id');
    }
}
