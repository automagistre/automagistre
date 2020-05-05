<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function strpos;

final class Version20200426224031 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE car_model DROP CONSTRAINT IF EXISTS fk_83ef70e2ee4789a');
        $this->addSql('ALTER TABLE car_model DROP CONSTRAINT IF EXISTS fk_83ef70ea23b42d');
        $this->addSql('DROP INDEX idx_83ef70ea23b42d');
        $this->addSql('ALTER TABLE car_model ALTER manufacturer_id TYPE VARCHAR USING manufacturer_id::varchar');
        $this->addSql('UPDATE car_model t SET manufacturer_id = v.uuid FROM (SELECT id, uuid FROM manufacturer) AS v WHERE t.manufacturer_id::int = v.id');
        $this->addSql('ALTER TABLE car_model ALTER manufacturer_id TYPE UUID USING manufacturer_id::uuid');
        $this->addSql('ALTER TABLE car_model ALTER manufacturer_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car_model.manufacturer_id IS \'(DC2Type:manufacturer_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        throw new LogicException('Not implemented');
    }
}
