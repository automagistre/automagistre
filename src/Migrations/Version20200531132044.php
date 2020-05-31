<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function implode;
use Ramsey\Uuid\Uuid;
use function sprintf;
use function strpos;

final class Version20200531132044 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('CREATE TABLE calendar_entry_order (id UUID NOT NULL, entry_id UUID DEFAULT NULL, customer_id UUID DEFAULT NULL, car_id UUID DEFAULT NULL, description TEXT DEFAULT NULL, worker_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1CF4E75BA364942 ON calendar_entry_order (entry_id)');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.customer_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.car_id IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_order.worker_id IS \'(DC2Type:employee_id)\'');
        $this->addSql('CREATE TABLE calendar_entry_schedule (id UUID NOT NULL, entry_id UUID DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, duration VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_86FDAEE3BA364942 ON calendar_entry_schedule (entry_id)');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.entry_id IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry_schedule.duration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE calendar_entry_order ADD CONSTRAINT FK_1CF4E75BA364942 FOREIGN KEY (entry_id) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE calendar_entry_schedule ADD CONSTRAINT FK_86FDAEE3BA364942 FOREIGN KEY (entry_id) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE calendar_entry DROP CONSTRAINT fk_47759e1e2cf9ddc');
        $this->addSql('DROP INDEX uniq_47759e1e2cf9ddc');

        // Data migration
        $rootEntries = $this->connection->fetchAll(
            'SELECT e1.id 
                    FROM calendar_entry e1 
                    LEFT JOIN calendar_entry e2 ON e2.previous = e1.id
                    WHERE e1.previous IS NULL AND e2.id IS NULL'
        );

        foreach ($rootEntries as ['id' => $rootEntryId]) {
            $scheduleId = Uuid::uuid6()->toString();
            $this->addSql(
                sprintf(
                    'INSERT INTO calendar_entry_schedule 
                            (id, entry_id, duration, date) 
                            SELECT \'%s\'::uuid, id, duration, date
                            FROM calendar_entry
                            WHERE id = \'%s\'::uuid',
                    $scheduleId,
                    $rootEntryId
                )
            );
            $this->addSql(
                sprintf(
                    'INSERT INTO created_by (id, user_id, created_at) 
                            SELECT \'%s\'::uuid, cb.user_id, cb.created_at 
                            FROM created_by cb
                            WHERE cb.id = \'%s\'::uuid',
                    $scheduleId,
                    $rootEntryId
                )
            );

            $orderId = Uuid::uuid6()->toString();
            $this->addSql(
                sprintf(
                    'INSERT INTO calendar_entry_order 
                            (id, entry_id, car_id, customer_id, worker_id, description)
                            SELECT \'%s\'::uuid, id, car_id, customer_id, worker_id, description
                            FROM calendar_entry
                            WHERE id = \'%s\'::uuid',
                    $orderId,
                    $rootEntryId
                )
            );
            $this->addSql(
                sprintf(
                    'INSERT INTO created_by (id, user_id, created_at) 
                            SELECT \'%s\'::uuid, cb.user_id, cb.created_at 
                            FROM created_by cb
                            WHERE cb.id = \'%s\'::uuid',
                    $orderId,
                    $rootEntryId
                )
            );
        }

        foreach ([6, 5, 4, 3, 2] as $count) {
            $selects = ['d.id AS deletion_id'];
            $joins = ['LEFT JOIN calendar_entry_deletion d ON e1.id = d.entry_id'];

            for ($i = 1; $i < $count + 1; ++$i) {
                $selects[] = sprintf('e%s.id AS e%s', $i, $i);
                if ($i > 1) {
                    $joins[] = sprintf('JOIN calendar_entry e%s ON e%s.previous = e%s.id', $i, $i, $i - 1);
                }
            }

            $select = implode(', ', $selects);
            $join = implode(' ', $joins);
            $entries = $this->connection->fetchAll("SELECT {$select} FROM calendar_entry e1 {$join}");

            foreach ($entries as $row) {
                $currentId = $row[sprintf('e%s', $count)];

                $this->addSql(
                    sprintf(
                        'INSERT INTO calendar_entry_schedule 
                                (id, entry_id, duration, date) 
                                SELECT id, \'%s\'::uuid, duration, date
                                FROM calendar_entry
                                WHERE id = \'%s\'::uuid',
                        $row['e1'],
                        $currentId,
                    )
                );
                $this->addSql(
                    sprintf(
                        'INSERT INTO calendar_entry_order 
                                (id, entry_id, car_id, customer_id, worker_id, description)
                                SELECT id, \'%s\'::uuid, car_id, customer_id, worker_id, description
                                FROM calendar_entry
                                WHERE id = \'%s\'::uuid',
                        $row['e1'],
                        $currentId,
                    )
                );
                $this->addSql(sprintf('DELETE FROM calendar_entry WHERE id = \'%s\'::uuid', $currentId));

                if (null !== $row['deletion_id']) {
                    $this->addSql(
                        sprintf(
                            'UPDATE calendar_entry_deletion SET entry_id = \'%s\'::uuid WHERE id = \'%s\'::uuid',
                            $row['e1'],
                            $row['deletion_id']
                        )
                    );
                }
            }
        }
        // Data migration

        $this->addSql('ALTER TABLE calendar_entry DROP previous');
        $this->addSql('ALTER TABLE calendar_entry DROP date');
        $this->addSql('ALTER TABLE calendar_entry DROP duration');
        $this->addSql('ALTER TABLE calendar_entry DROP description');
        $this->addSql('ALTER TABLE calendar_entry DROP car_id');
        $this->addSql('ALTER TABLE calendar_entry DROP customer_id');
        $this->addSql('ALTER TABLE calendar_entry DROP worker_id');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('DROP TABLE calendar_entry_order');
        $this->addSql('DROP TABLE calendar_entry_schedule');
        $this->addSql('ALTER TABLE calendar_entry ADD previous UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD duration VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD car_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD customer_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD worker_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN calendar_entry.previous IS \'(DC2Type:calendar_entry_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.duration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.car_id IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.customer_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.worker_id IS \'(DC2Type:employee_id)\'');
        $this->addSql('ALTER TABLE calendar_entry ADD CONSTRAINT fk_47759e1e2cf9ddc FOREIGN KEY (previous) REFERENCES calendar_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_47759e1e2cf9ddc ON calendar_entry (previous)');
    }
}
