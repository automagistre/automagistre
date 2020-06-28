<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200628190404 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE expense ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE expense ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN expense.id IS \'(DC2Type:expense_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE order_number INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE expense ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE expense ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN expense.id IS \'(DC2Type:uuid)\'');
    }
}
