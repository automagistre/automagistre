<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200513141440 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE income_part ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE income_part ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN income_part.uuid IS \'(DC2Type:income_part_id)\'');

        //> Migration data
        $this->addSql('
        UPDATE motion
        SET source_id = b.uuid, description = NULL
        FROM (
                 SELECT motion.id, orders.uuid
                 from motion
                          JOIN orders ON orders.id = replace(motion.description, \'Заказ #\', \'\')::integer
                 WHERE motion.description LIKE \'Заказ #%\' AND source = 3
             ) b
        WHERE motion.id = b.id;
        ');
        $this->addSql('
        UPDATE motion
        SET source_id = b.uuid, description = NULL
        FROM (
                 SELECT motion.id, orders.uuid
                 from motion
                          JOIN orders ON orders.id = replace(motion.description, \'Order #\', \'\')::integer
                 WHERE motion.description LIKE \'Order #%\' AND source = 3
             ) b
        WHERE motion.id = b.id;
        ');
        $this->addSql('
        UPDATE motion SET description = NULL WHERE description LIKE \'# Приход #%\' AND source = 2;
        ');
        //< Migration data
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE income_part ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE income_part ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN income_part.uuid IS \'(DC2Type:uuid)\'');
    }
}
