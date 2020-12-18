<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Review\Entity\ReviewView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201216184629 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS review_view');
        $this->addSql(ReviewView::sql());
    }
}
