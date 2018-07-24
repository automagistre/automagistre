<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170427144207 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE part SET price = price * 100 WHERE price > 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE part SET price = price / 100 WHERE price > 0');
    }
}
