<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;
use function sprintf;

final class Version20200619160922 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE monthly_salary ADD uuid UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN monthly_salary.uuid IS \'(DC2Type:uuid)\'');
        foreach ($this->connection->fetchAll('SELECT id FROM monthly_salary order by id') as $row) {
            $this->addSql(sprintf(
                'UPDATE monthly_salary SET uuid = \'%s\'::uuid WHERE id = %s',
                Uuid::uuid6()->toString(),
                $row['id'],
            ));
        }
        $this->addSql('ALTER TABLE monthly_salary ALTER uuid SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE monthly_salary DROP uuid');
    }
}
