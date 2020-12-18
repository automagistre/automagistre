<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Review\Entity\ReviewView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201123211624 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE review ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE review ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN review.id IS \'(DC2Type:review_id)\'');
        $this->addSql(ReviewView::sql());
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE review ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE review ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN review.id IS \'(DC2Type:uuid)\'');
    }
}
