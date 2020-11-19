<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201119071145 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS order_number');
        $this->addSql('SELECT setval(\'order_number\', (SELECT MAX(number::INTEGER) FROM orders))');
    }
}
