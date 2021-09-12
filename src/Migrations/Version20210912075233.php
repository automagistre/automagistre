<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210912075233 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE orders ALTER number TYPE INT USING number::INT');
        $this->addSql('ALTER TABLE orders ALTER number DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE orders ALTER number TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE orders ALTER number DROP DEFAULT');
    }
}
