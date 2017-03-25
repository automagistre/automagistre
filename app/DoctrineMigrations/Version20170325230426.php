<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170325230426 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO part (name, manufacturer_id, number, reserved) VALUES (\'ЗДЕСЬ БЫЛА ЗАПЧАСТЬ КОТОРУЮ УДАЛИЛИ\', 1, \'----------\', 0)');

        $this->addSql('UPDATE order_part SET part_id = (SELECT id FROM part WHERE part.number = \'----------\') WHERE part_id IS NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
