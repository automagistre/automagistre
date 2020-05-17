<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Employee\Entity\EmployeeId;
use function array_map;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function sprintf;
use function strpos;

final class Version20200517225439 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE employee ADD uuid UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN employee.uuid IS \'(DC2Type:employee_id)\'');

        //> Data migration
        $ids = $this->connection->fetchAll('SELECT id FROM employee');
        $ids = array_map('array_shift', $ids);

        foreach ($ids as $id) {
            $this->addSql(
                sprintf(
                    'UPDATE employee SET uuid = \'%s\'::uuid WHERE id = %s AND uuid IS NULL',
                    EmployeeId::generate()->toString(),
                    $id
                )
            );
        }
        //< Data migration

        $this->addSql('ALTER TABLE employee ALTER uuid SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE employee DROP uuid');
    }
}
