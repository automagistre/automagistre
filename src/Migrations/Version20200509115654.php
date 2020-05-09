<?php

declare(strict_types=1);

namespace App\Migrations;

use function array_reduce;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function sprintf;
use function strpos;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20200509115654 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $conn = $this->container->get('doctrine.dbal.landlord_connection');
        $users = $conn->executeQuery('SELECT id, uuid FROM users')->fetchAll();

        $case = array_reduce(
            $users,
            fn (string $case, array $row) => $case.sprintf(' WHEN %s = created_by_id THEN \'%s\'::uuid', $row['id'], $row['uuid']),
            ''
        );

        if ('' !== $case) {
            $case = 'CASE '.$case.' END';
            $this->addSql('INSERT INTO created_by (id, user_id, created_at) SELECT id, ('.$case.'), created_at FROM income');
        }

        $this->addSql('ALTER TABLE income DROP created_at');
        $this->addSql('ALTER TABLE income DROP created_by_id');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE income ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE income ADD created_by_id INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN income.created_at IS \'(DC2Type:datetime_immutable)\'');
    }
}
