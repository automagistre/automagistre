<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200316200935 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE calendar_entry ADD first_name VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD last_name VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD phone VARCHAR(35) DEFAULT NULL');
        $this->addSql('ALTER TABLE calendar_entry ADD car_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN calendar_entry.phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN calendar_entry.car_id IS \'(DC2Type:car_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE calendar_entry DROP first_name');
        $this->addSql('ALTER TABLE calendar_entry DROP last_name');
        $this->addSql('ALTER TABLE calendar_entry DROP phone');
        $this->addSql('ALTER TABLE calendar_entry DROP car_id');
    }
}
