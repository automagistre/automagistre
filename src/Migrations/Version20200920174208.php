<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Part\Entity\PartView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200920174208 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS part_view');
        $this->addSql(PartView::sql());
    }

    public function down(Schema $schema): void
    {
    }
}
