<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Costil;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201214161606 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM created_by WHERE id IN (SELECT id FROM publish) AND user_id = \''.Costil::SERVICE_USER.'\'');

        $this->addSql('DELETE FROM publish WHERE id IN (select id FROM publish LEFT JOIN created_by USING (id) WHERE created_at IS NULL)');
    }
}
