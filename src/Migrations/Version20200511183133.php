<?php

declare(strict_types=1);

namespace App\Migrations;

use function array_reduce;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function sprintf;
use function strpos;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20200511183133 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        /** @var Connection $landlord */
        $landlord = $this->container->get('doctrine.dbal.landlord_connection');

        $this->addSql('ALTER TABLE motion ADD source SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE motion ADD source_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN motion.source IS \'(DC2Type:motion_source_enum)\'');
        $this->addSql('COMMENT ON COLUMN motion.source_id IS \'(DC2Type:uuid)\'');

        //> Migrate order source
        $this->addSql('
            UPDATE motion
            SET source    = 3,
                source_id = b.uuid
            FROM (
                     SELECT mo.id, o.uuid
                     FROM orders o
                              JOIN motion_order mo ON o.id = mo.order_id
                 ) b
            WHERE motion.id = b.id
        ');
        //< Migrate order source

        //> Migrate user source
        $users = $landlord->executeQuery('SELECT id, uuid FROM users')->fetchAll();

        $whenThen = array_reduce(
            $users,
            fn (
                string $case,
                array $row
            ) => $case.sprintf(' WHEN %s = user_id THEN \'%s\'::uuid', $row['id'], $row['uuid']),
            ''
        );

        if ('' !== $whenThen) {
            $this->addSql('
            UPDATE motion SET source = 1, source_id = b.uuid
                FROM (
                SELECT mm.id, CASE '.$whenThen.' END AS uuid FROM motion_manual mm
                ) b
            WHERE motion.id = b.id
        ');
        }
        //< Migrate user source

        //> Migrate income motion
        $this->addSql('
            UPDATE motion
            SET source    = 2,
                source_id = b.uuid
            FROM (
                     SELECT mi.id, ip.uuid
                     FROM income_part ip
                              JOIN motion_income mi ON ip.id = mi.income_part_id
                 ) b
            WHERE motion.id = b.id
            ');
        //< Migrate income motion

        $this->addSql('UPDATE motion SET source = 0, source_id = \'4ffc24e2-8e60-42e0-9c8f-7a73888b2da6\'::uuid WHERE source IS NULL'); // uuid of old@automagistre.ru

        $this->addSql('ALTER TABLE income_part DROP CONSTRAINT fk_834566e8ffe2c7');
        $this->addSql('ALTER TABLE income_part DROP accrued_motion_id');

        $this->addSql('DROP TABLE motion_income');
        $this->addSql('DROP TABLE motion_order');
        $this->addSql('DROP TABLE motion_manual');
        $this->addSql('DROP TABLE motion_old');
        $this->addSql('ALTER TABLE orders ALTER uuid SET NOT NULL');
        $this->addSql('ALTER TABLE motion DROP type');

        $this->addSql('ALTER TABLE motion ALTER source SET NOT NULL');
        $this->addSql('ALTER TABLE motion ALTER source_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        throw new LogicException('Nope.');
    }
}
