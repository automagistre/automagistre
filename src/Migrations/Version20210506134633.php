<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210506134633 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE inventorization (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN inventorization.id IS \'(DC2Type:inventorization_id)\'');
        $this->addSql('CREATE TABLE inventorization_close (id UUID NOT NULL, inventorization_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4F6195A04CA655FD ON inventorization_close (inventorization_id)');
        $this->addSql('COMMENT ON COLUMN inventorization_close.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN inventorization_close.inventorization_id IS \'(DC2Type:inventorization_id)\'');
        $this->addSql('CREATE TABLE inventorization_part (part_id UUID NOT NULL, inventorization_id UUID DEFAULT NULL, quantity INT NOT NULL, PRIMARY KEY(inventorization_id, part_id))');
        $this->addSql('COMMENT ON COLUMN inventorization_part.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN inventorization_part.inventorization_id IS \'(DC2Type:inventorization_id)\'');
        $this->addSql('ALTER TABLE inventorization_close ADD CONSTRAINT FK_4F6195A04CA655FD FOREIGN KEY (inventorization_id) REFERENCES inventorization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inventorization_close DROP CONSTRAINT FK_4F6195A04CA655FD');
        $this->addSql('ALTER TABLE inventorization_part DROP CONSTRAINT FK_9A71107D4CA655FD');
        $this->addSql('DROP TABLE inventorization');
        $this->addSql('DROP TABLE inventorization_close');
        $this->addSql('DROP TABLE inventorization_part');
    }
}
