<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201123114504 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mc_line ADD position INT DEFAULT NULL');
        $this->addSql('UPDATE mc_line SET position = 0 WHERE position IS NULL');
        $this->addSql('ALTER TABLE mc_line ALTER position SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mc_line DROP position');
    }
}
