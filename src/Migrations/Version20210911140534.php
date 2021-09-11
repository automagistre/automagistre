<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210911140534 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM migration_versions WHERE version NOT IN (:one, :two)', [
            __CLASS__,
            Version20210911132533::class,
        ]);
    }
}
