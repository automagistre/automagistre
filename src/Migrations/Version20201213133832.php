<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201213133832 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE publish (id UUID NOT NULL, entity_id UUID NOT NULL, published BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN publish.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN publish.entity_id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE publish');
    }
}
