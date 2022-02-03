<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220203165915 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cron_report ADD error TEXT DEFAULT NULL');
        $this->addSql('UPDATE cron_report SET error = \'\'');
        $this->addSql('ALTER TABLE cron_report ALTER error SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cron_report DROP error');
    }
}
