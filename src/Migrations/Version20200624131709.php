<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200624131709 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE employee ADD person_uuid UUID DEFAULT NULL');
        $this->addSql('UPDATE employee SET person_uuid = sub.uuid FROM (SELECT id, uuid FROM operand) sub WHERE sub.id = employee.person_id');
        $this->addSql('ALTER TABLE employee DROP person_id');
        $this->addSql('ALTER TABLE employee RENAME person_uuid TO person_id');
        $this->addSql('ALTER TABLE employee ALTER person_id DROP DEFAULT');
        $this->addSql('ALTER TABLE employee ALTER person_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN employee.person_id IS \'(DC2Type:operand_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE employee ALTER person_id TYPE INT');
        $this->addSql('ALTER TABLE employee ALTER person_id DROP DEFAULT');
        $this->addSql('ALTER TABLE employee ALTER person_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN employee.person_id IS NULL');
    }
}
