<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Part\Entity\PartView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201001163656 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE part ADD unit SMALLINT DEFAULT 1');
        $this->addSql('ALTER TABLE part ALTER unit DROP DEFAULT');
        $this->addSql('ALTER TABLE part ALTER unit SET NOT NULL ');
        $this->addSql('COMMENT ON COLUMN part.unit IS \'(DC2Type:unit_enum)\'');

        $this->addSql('DROP VIEW IF EXISTS part_view');
        $this->addSql(PartView::sql());
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE part DROP unit');
    }
}
