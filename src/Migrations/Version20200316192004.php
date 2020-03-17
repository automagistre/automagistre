<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200316192004 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'Landlord only');

        $this->addSql('ALTER TABLE car ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE car ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car.uuid IS \'(DC2Type:car_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'Landlord only');

        $this->addSql('ALTER TABLE car ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE car ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car.uuid IS \'(DC2Type:uuid)\'');
    }
}
