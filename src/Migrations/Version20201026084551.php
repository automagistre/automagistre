<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201026084551 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE yandex_map_review (id UUID NOT NULL, review_id VARCHAR(255) NOT NULL, payload JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN yandex_map_review.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE yandex_map_review');
    }
}
