<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210329223003 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mc_work ADD comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE mc_work SET comment = description');
        $this->addSql('UPDATE mc_work SET description = NULL');
    }
}
