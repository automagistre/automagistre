<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210505140435 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE inventorization');
        $this->addSql('ALTER TABLE motion RENAME COLUMN source TO source_type');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE inventorization (id UUID NOT NULL, part_id UUID DEFAULT NULL, quantity INT NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_6567f6f84ce34bec ON inventorization (part_id)');
        $this->addSql('COMMENT ON COLUMN inventorization.id IS \'(DC2Type:inventorization_id)\'');
        $this->addSql('COMMENT ON COLUMN inventorization.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('ALTER TABLE inventorization ADD CONSTRAINT fk_6567f6f84ce34bec FOREIGN KEY (part_id) REFERENCES storage_part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE motion RENAME COLUMN source_type TO source');
    }
}
