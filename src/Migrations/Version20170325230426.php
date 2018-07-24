<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170325230426 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO part (name, manufacturer_id, number, reserved) VALUES (\'ЗДЕСЬ БЫЛА ЗАПЧАСТЬ КОТОРУЮ УДАЛИЛИ\', 1, \'----------\', 0)');

        $this->addSql('UPDATE order_part SET part_id = (SELECT id FROM part WHERE part.number = \'----------\') WHERE part_id IS NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
