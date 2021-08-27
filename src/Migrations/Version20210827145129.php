<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210827145129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update views';
    }

    public function up(Schema $schema): void
    {
    }
}
