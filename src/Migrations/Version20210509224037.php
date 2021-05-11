<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210509224037 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('COMMENT ON COLUMN reservation.id IS \'(DC2Type:reservation_id)\'');
    }
}
