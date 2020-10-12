<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200830224533 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE car SET identifier = NULL WHERE identifier = \'\'');
        $this->addSql('UPDATE car SET identifier = UPPER(identifier) WHERE identifier IS NOT NULL AND identifier <> UPPER(identifier)');
        $this->addSql('UPDATE vehicle_model SET case_name = UPPER(case_name) WHERE case_name IS NOT NULL AND case_name <> UPPER(case_name)');
    }
}
