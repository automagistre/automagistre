<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210820124530 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER SEQUENCE order_number RENAME TO order_number_seq');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER SEQUENCE order_number_seq RENAME TO order_number');
    }
}
