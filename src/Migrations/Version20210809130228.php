<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210809130228 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE note_delete (id UUID NOT NULL, note_id UUID DEFAULT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_22C02B5326ED0855 ON note_delete (note_id)');
        $this->addSql('COMMENT ON COLUMN note_delete.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN note_delete.note_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE
          note_delete
        ADD
          CONSTRAINT FK_22C02B5326ED0855 FOREIGN KEY (note_id) REFERENCES note (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE note_delete');
    }
}
