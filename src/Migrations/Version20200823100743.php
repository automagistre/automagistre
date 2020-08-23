<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200823100743 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE order_number CASCADE');
        $this->addSql('DROP INDEX uniq_b53af235a23b42ddf3ba4b5');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B53AF235A23B42D5E237E06DF3BA4B5 ON vehicle_model (manufacturer_id, name, case_name)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE order_number INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP INDEX UNIQ_B53AF235A23B42D5E237E06DF3BA4B5');
        $this->addSql('CREATE UNIQUE INDEX uniq_b53af235a23b42ddf3ba4b5 ON vehicle_model (manufacturer_id, case_name)');
    }
}
