<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190704155154 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('tenant' !== $this->connection->getDatabase(), 'Tenant only');

        $this->addSql('ALTER TABLE order_item_part ADD hidden TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE order_item_service ADD hidden TINYINT(1) NOT NULL');

        $this->addSql('
            UPDATE order_item_part oip
            SET oip.hidden = 1
            WHERE oip.id IN (
                SELECT oip.id
                FROM order_item_part oip
                         JOIN order_item o on oip.id = o.id
                         JOIN order_item_group oig ON oig.id = o.parent_id AND oig.hide_parts IS TRUE
                UNION
                SELECT oip.id
                FROM order_item_part oip
                         JOIN order_item op ON oip.id = op.id
                         JOIN order_item_service ois ON ois.id = op.parent_id
                         JOIN order_item os ON os.id = ois.id
                         JOIN order_item_group oig ON oig.id = os.parent_id
            )
        ');

        $this->addSql('ALTER TABLE order_item_group DROP hide_parts');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('tenant' !== $this->connection->getDatabase(), 'Tenant only');

        $this->addSql('ALTER TABLE order_item_group ADD hide_parts TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE order_item_part DROP hidden');
        $this->addSql('ALTER TABLE order_item_service DROP hidden');
    }
}
