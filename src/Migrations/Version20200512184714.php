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

final class Version20200512184714 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE order_item_service ADD worker_uuid UUID DEFAULT NULL');

        //> Data migrations
        /** @var Connection $landlord */
        $landlord = $this->container->get('doctrine.dbal.landlord_connection');
        $tenant = $this->connection;

        $operands = $tenant->fetchAll('SELECT DISTINCT worker_id FROM order_item_service');
        $operands = array_map('array_shift', $operands);
        $operands = $landlord->fetchAll('SELECT id, uuid FROM operand WHERE id IN (:ids)', ['ids' => $operands], ['ids' => Connection::PARAM_INT_ARRAY]);

        $whenThen = array_reduce(
            $operands,
            fn (
                string $case,
                array $row
            ) => $case.sprintf(' WHEN %s = worker_id THEN \'%s\'::uuid', $row['id'], $row['uuid']),
            ''
        );

        if ('' !== $whenThen) {
            $whenThen = 'CASE '.$whenThen.' END';
            $this->addSql('UPDATE order_item_service SET worker_uuid = '.$whenThen);
        }
        //< Data migrations

        $this->addSql('ALTER TABLE order_item_service DROP worker_id');
        $this->addSql('ALTER TABLE order_item_service RENAME worker_uuid TO worker_id');
        $this->addSql('COMMENT ON COLUMN order_item_service.worker_id IS \'(DC2Type:operand_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        throw new LogicException('Nope.');
    }
}
