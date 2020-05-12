<?php

declare(strict_types=1);

namespace App\Migrations;

use function array_map;
use function array_reduce;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function sprintf;
use function strpos;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20200511190211 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE income_part ADD part_uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item_part ADD part_uuid UUID DEFAULT NULL');

        //> Migrate income
        /** @var Connection $landlord */
        $landlord = $this->container->get('doctrine.dbal.landlord_connection');
        $parts = $this->connection->fetchAll('SELECT DISTINCT part_id FROM (SELECT part_id FROM income_part UNION SELECT part_id FROM order_item_part) a');
        $parts = array_map('array_shift', $parts);
        $parts = $landlord->fetchAll('SELECT id, part_id AS uuid FROM part WHERE id IN (:ids)', ['ids' => $parts], ['ids' => Connection::PARAM_INT_ARRAY]);

        $whenThen = array_reduce(
            $parts,
            fn (
                string $case,
                array $row
            ) => $case.sprintf(' WHEN part_id = %s THEN \'%s\'::uuid', $row['id'], $row['uuid']),
            ''
        );

        if ('' !== $whenThen) {
            $this->addSql('
                UPDATE income_part SET part_uuid = b.uuid
                    FROM (
                    SELECT ip.id, CASE '.$whenThen.' END AS uuid FROM income_part ip
                    ) b
                WHERE income_part.id = b.id
            ');
            $this->addSql('
                UPDATE order_item_part SET part_uuid = b.uuid
                    FROM (
                    SELECT oip.id, CASE '.$whenThen.' END AS uuid FROM order_item_part oip
                    ) b
                WHERE order_item_part.id = b.id
            ');
        }
        //< Migrate data

        $this->addSql('ALTER TABLE income_part DROP part_id');
        $this->addSql('ALTER TABLE income_part RENAME part_uuid TO part_id');
        $this->addSql('ALTER TABLE income_part ALTER part_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN income_part.part_id IS \'(DC2Type:part_id)\'');

        $this->addSql('ALTER TABLE order_item_part ALTER part_uuid SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN order_item_part.part_uuid IS \'(DC2Type:part_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        throw new LogicException('Nope.');
    }
}
