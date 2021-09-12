<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210912142750 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE motion DROP CONSTRAINT fk_f5fea1e84ce34bec');
        $this->addSql('DROP TABLE storage_part');
    }
}
