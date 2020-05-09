<?php

declare(strict_types=1);

namespace App\Migrations;

use function array_map;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function sprintf;
use function strpos;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20200509143743 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $suppliers = $this->connection->fetchAll('SELECT DISTINCT supplier_id FROM income');
        $suppliers = array_map('array_shift', $suppliers);

        $uuids = $this->container->get('doctrine.dbal.landlord_connection')->fetchAll(
            'SELECT id, uuid FROM operand WHERE id IN (:ids)',
            [
                'ids' => $suppliers,
            ],
            [
                'ids' => Connection::PARAM_INT_ARRAY,
            ]
        );

        $this->addSql('ALTER TABLE income ADD supplier_uuid UUID DEFAULT NULL');
        foreach ($uuids as $uuid) {
            $this->addSql(sprintf('UPDATE income SET supplier_uuid = \'%s\'::uuid WHERE supplier_id = %s', $uuid['uuid'], $uuid['id']));
        }
        $this->addSql('ALTER TABLE income DROP supplier_id');
        $this->addSql('ALTER TABLE income RENAME supplier_uuid TO supplier_id');
        $this->addSql('ALTER TABLE income ALTER supplier_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN income.supplier_id IS \'(DC2Type:operand_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE income ADD supplier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE income DROP supplier');
    }
}
