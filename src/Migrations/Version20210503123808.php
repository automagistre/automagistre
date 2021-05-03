<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210503123808 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE storage_part (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN storage_part.id IS \'(DC2Type:part_id)\'');
        $this->addSql('INSERT INTO storage_part (id) SELECT DISTINCT part_id FROM motion');

        $this->addSql('CREATE TABLE inventorization (id UUID NOT NULL, part_id UUID DEFAULT NULL, quantity INT NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6567F6F84CE34BEC ON inventorization (part_id)');
        $this->addSql('COMMENT ON COLUMN inventorization.id IS \'(DC2Type:inventorization_id)\'');
        $this->addSql('COMMENT ON COLUMN inventorization.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('ALTER TABLE inventorization ADD CONSTRAINT FK_6567F6F84CE34BEC FOREIGN KEY (part_id) REFERENCES storage_part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE motion ALTER part_id DROP NOT NULL');
        $this->addSql('ALTER TABLE motion ADD CONSTRAINT FK_F5FEA1E84CE34BEC FOREIGN KEY (part_id) REFERENCES storage_part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F5FEA1E84CE34BEC ON motion (part_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inventorization DROP CONSTRAINT FK_6567F6F84CE34BEC');
        $this->addSql('ALTER TABLE motion DROP CONSTRAINT FK_F5FEA1E84CE34BEC');
        $this->addSql('DROP TABLE inventorization');
        $this->addSql('DROP TABLE storage_part');
        $this->addSql('DROP INDEX IDX_F5FEA1E84CE34BEC');
        $this->addSql('ALTER TABLE motion ALTER part_id SET NOT NULL');
    }
}
