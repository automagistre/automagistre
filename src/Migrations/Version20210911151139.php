<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function is_string;

final class Version20210911151139 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $identifier = getenv('TENANT');

        if (!is_string($identifier)) {
            return;
        }

        $this->addSql("UPDATE cron_job SET command = command || ' --tenant={$identifier}'");

        if ('kazan' === $identifier) {
            $this->addSql('DROP INDEX idx_b6c6a7f5be04ea9');
            $this->addSql('ALTER TABLE cron_report DROP CONSTRAINT fk_b6c6a7f5be04ea9');
            $this->addSql('UPDATE cron_job SET id = 6 WHERE id = 1');
            $this->addSql('UPDATE cron_report SET job_id = 6 WHERE job_id = 1');
            $this->addSql(
                'ALTER TABLE cron_report ADD CONSTRAINT FK_B6C6A7F5BE04EA9 FOREIGN KEY (job_id) REFERENCES cron_job (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE',
            );
            $this->addSql('CREATE INDEX IDX_B6C6A7F5BE04EA9 ON cron_report (job_id)');
        }
    }
}
