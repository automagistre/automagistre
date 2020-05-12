<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200512230522 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE operand_note ADD created_by UUID DEFAULT NULL');
        //> Migrate User
        $this->addSql('
            UPDATE operand_note
            SET created_by = b.uuid
            FROM (
                     SELECT bon.id, u.uuid
                     FROM users u
                              JOIN operand_note bon ON bon.created_by_id = u.id
                 ) b
            WHERE operand_note.id = b.id
        ');
        //< Migrate User

        $this->addSql('ALTER TABLE operand_note DROP CONSTRAINT fk_36bde441b03a8386');
        $this->addSql('DROP INDEX idx_36bde441b03a8386');

        $this->addSql('ALTER TABLE operand_note ALTER created_by SET NOT NULL');
        $this->addSql('ALTER TABLE operand_note DROP created_by_id');
        $this->addSql('COMMENT ON COLUMN operand_note.created_by IS \'(DC2Type:user_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE operand_note ADD created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE operand_note DROP created_by');
        $this->addSql('ALTER TABLE 
          operand_note 
        ADD 
          CONSTRAINT fk_36bde441b03a8386 FOREIGN KEY (created_by_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_36bde441b03a8386 ON operand_note (created_by_id)');
    }
}
