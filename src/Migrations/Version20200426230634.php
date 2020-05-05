<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200426230634 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE part DROP CONSTRAINT fk_490f70c6a23b42d');
        $this->addSql('DROP INDEX idx_490f70c6a23b42d');

        $this->addSql('ALTER TABLE part ALTER manufacturer_id TYPE VARCHAR');
        $this->addSql('UPDATE part t SET manufacturer_id = v.uuid FROM (SELECT id, uuid FROM manufacturer) AS v WHERE t.manufacturer_id::int = v.id');
        $this->addSql('ALTER TABLE part ALTER manufacturer_id TYPE UUID USING manufacturer_id::uuid');
        $this->addSql('ALTER TABLE part ALTER manufacturer_id DROP DEFAULT');

        $this->addSql('COMMENT ON COLUMN part.manufacturer_id IS \'(DC2Type:manufacturer_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE part ALTER manufacturer_id TYPE INT');
        $this->addSql('ALTER TABLE part ALTER manufacturer_id DROP DEFAULT');
        $this->addSql('ALTER TABLE part ALTER manufacturer_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN part.manufacturer_id IS NULL');
        $this->addSql('ALTER TABLE 
          part 
        ADD 
          CONSTRAINT fk_490f70c6a23b42d FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_490f70c6a23b42d ON part (manufacturer_id)');
    }
}
