<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Part\Entity\PartView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200803140901 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE part_supply (id UUID NOT NULL, part_id UUID NOT NULL, supplier_id UUID NOT NULL, quantity INT NOT NULL, source SMALLINT NOT NULL, source_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN part_supply.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_supply.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_supply.supplier_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN part_supply.source IS \'(DC2Type:part_supply_source_enum)\'');
        $this->addSql('COMMENT ON COLUMN part_supply.source_id IS \'(DC2Type:uuid)\'');

        $this->addSql('DROP VIEW IF EXISTS part_view');
        $this->addSql(PartView::sql());
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE supply');
    }
}
