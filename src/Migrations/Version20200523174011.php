<?php

declare(strict_types=1);

namespace App\Migrations;

use function array_map;
use function array_reduce;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function sprintf;
use function strpos;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20200523174011 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE motion ADD part_uuid UUID DEFAULT NULL');

        //> Migrate data
        /** @var Connection $landlord */
        $landlord = $this->container->get('doctrine.dbal.landlord_connection');
        $parts = $this->connection->fetchAll('SELECT DISTINCT part_id FROM motion');
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
                UPDATE motion SET part_uuid = b.uuid
                    FROM (
                    SELECT m.id, CASE '.$whenThen.' END AS uuid FROM motion m
                    ) b
                WHERE motion.id = b.id
            ');
        }
        //< Migrate data

        $this->addSql('ALTER TABLE motion DROP part_id');
        $this->addSql('ALTER TABLE motion RENAME part_uuid TO part_id');
        $this->addSql('ALTER TABLE motion ALTER part_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN motion.part_id IS \'(DC2Type:part_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE motion ALTER part_id TYPE INT');
        $this->addSql('ALTER TABLE motion ALTER part_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN motion.part_id IS NULL');
    }
}
