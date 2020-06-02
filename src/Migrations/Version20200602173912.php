<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200602173912 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('CREATE TABLE sms (id UUID NOT NULL, phone_number VARCHAR(35) NOT NULL, message VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sms.id IS \'(DC2Type:sms_id)\'');
        $this->addSql('COMMENT ON COLUMN sms.phone_number IS \'(DC2Type:phone_number)\'');

        $this->addSql('CREATE TABLE sms_send (id UUID NOT NULL, sms_id UUID NOT NULL, success BOOLEAN NOT NULL, payload JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sms_send.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sms_send.sms_id IS \'(DC2Type:sms_id)\'');

        $this->addSql('CREATE TABLE sms_status (id UUID NOT NULL, sms_id UUID NOT NULL, payload JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sms_status.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sms_status.sms_id IS \'(DC2Type:sms_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('DROP TABLE sms');
        $this->addSql('DROP TABLE sms_send');
        $this->addSql('DROP TABLE sms_status');
    }
}
