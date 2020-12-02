<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201202155334 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE expense DROP CONSTRAINT fk_2d3a8da6712520f3');
        $this->addSql('DROP INDEX idx_2d3a8da6712520f3');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT fk_2d3a8da6712520f3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_2d3a8da6712520f3 ON expense (wallet_id)');
    }
}
