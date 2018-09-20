<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180916141323 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849554CE34BEC');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849558D9F6D38');
        $this->addSql('DROP INDEX IDX_42C849554CE34BEC ON reservation');
        $this->addSql('DROP INDEX IDX_42C849558D9F6D38 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP part_id, CHANGE order_id order_item_part_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955437EF9D2 FOREIGN KEY (order_item_part_id) REFERENCES order_item_part (id)');
        $this->addSql('CREATE INDEX IDX_42C84955437EF9D2 ON reservation (order_item_part_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955437EF9D2');
        $this->addSql('DROP INDEX IDX_42C84955437EF9D2 ON reservation');
        $this->addSql('ALTER TABLE reservation ADD part_id INT DEFAULT NULL, CHANGE order_item_part_id order_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849554CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849558D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_42C849554CE34BEC ON reservation (part_id)');
        $this->addSql('CREATE INDEX IDX_42C849558D9F6D38 ON reservation (order_id)');
    }
}
