<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200823102848 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE manufacturer ALTER name SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3D0AE6DC5E237E06 ON manufacturer (name)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX UNIQ_3D0AE6DC5E237E06');
        $this->addSql('ALTER TABLE manufacturer ALTER name DROP NOT NULL');
    }
}
