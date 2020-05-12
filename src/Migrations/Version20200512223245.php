<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200512223245 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE users ADD person_uuid uuid DEFAULT NULL');

        $this->addSql('ALTER TABLE users DROP CONSTRAINT fk_1483a5e9217bbb47');
        $this->addSql('DROP INDEX uniq_1483a5e9217bbb47');
        $this->addSql('ALTER TABLE users DROP person_id');
        $this->addSql('ALTER TABLE users RENAME person_uuid TO person_id');
        $this->addSql('COMMENT ON COLUMN users.person_id IS \'(DC2Type:operand_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE users ALTER person_id TYPE INT');
        $this->addSql('ALTER TABLE users ALTER person_id DROP DEFAULT');
        $this->addSql('ALTER TABLE users ALTER person_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN users.person_id IS NULL');
        $this->addSql('ALTER TABLE 
          users 
        ADD 
          CONSTRAINT fk_1483a5e9217bbb47 FOREIGN KEY (person_id) REFERENCES person (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_1483a5e9217bbb47 ON users (person_id)');
    }
}
