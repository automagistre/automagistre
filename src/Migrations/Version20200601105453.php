<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function strpos;

final class Version20200601105453 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('DROP VIEW IF EXISTS calendar_entry_view');
        $this->addSql('
            CREATE VIEW calendar_entry_view AS
            SELECT e.id,
                   ces.date         AS schedule_date,
                   ces.duration     AS schedule_duration,
                   ceoi.customer_id AS order_info_customer_id,
                   ceoi.car_id      AS order_info_car_id,
                   ceoi.description AS order_info_description,
                   ceoi.worker_id   AS order_info_worker_id,
                   ceo.order_id     AS order_id
            FROM calendar_entry e
                     LEFT JOIN calendar_entry_deletion ced on e.id = ced.entry_id
                     LEFT JOIN calendar_entry_order ceo ON ceo.entry_id = e.id
                     JOIN LATERAL (SELECT *
                                   FROM calendar_entry_schedule sub
                                   WHERE sub.entry_id = e.id
                                   ORDER BY sub.id DESC
                                   LIMIT 1
                ) ces ON true
                     JOIN LATERAL (SELECT *
                                   FROM calendar_entry_order_info sub
                                   WHERE sub.entry_id = e.id
                                   ORDER BY sub.id DESC
                                   LIMIT 1
                ) ceoi ON true
            WHERE ced IS NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        throw new LogicException('Nope.');
    }
}
