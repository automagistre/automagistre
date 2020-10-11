<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201011212829 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE part SET number = upper(number) WHERE number <> upper(number)');
    }

    public function down(Schema $schema): void
    {
    }
}
