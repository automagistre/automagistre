<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210820184144 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organization ADD email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE organization ADD contractor BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE organization ADD seller BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE organization ADD tenant_id SMALLINT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN organization.tenant_id IS \'(DC2Type:tenant_enum)\'');

        $this->addSql('UPDATE organization
            SET email=operand.email,
                contractor=operand.contractor,
                seller=operand.seller,
                tenant_id=operand.tenant_id
            FROM (SELECT id, email, contractor, seller, tenant_id FROM operand) operand
            WHERE organization.id = operand.id
            ');

        $this->addSql('ALTER TABLE organization ALTER contractor SET NOT NULL');
        $this->addSql('ALTER TABLE organization ALTER seller SET NOT NULL');

        $this->addSql('ALTER TABLE person ADD email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD contractor BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD seller BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD tenant_id SMALLINT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN person.tenant_id IS \'(DC2Type:tenant_enum)\'');

        $this->addSql('UPDATE person
            SET email=operand.email,
                contractor=operand.contractor,
                seller=operand.seller,
                tenant_id=operand.tenant_id
            FROM (SELECT id, email, contractor, seller, tenant_id FROM operand) operand
            WHERE person.id = operand.id');

        $this->addSql('ALTER TABLE person ALTER contractor SET NOT NULL');
        $this->addSql('ALTER TABLE person ALTER seller SET NOT NULL');

        $this->addSql('ALTER TABLE organization DROP CONSTRAINT fk_c1ee637cbf396750');
        $this->addSql('ALTER TABLE person DROP CONSTRAINT fk_34dcd176bf396750');
        $this->addSql('DROP TABLE operand');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE operand (
          id UUID NOT NULL,
          type VARCHAR(255) NOT NULL,
          contractor BOOLEAN NOT NULL,
          seller BOOLEAN NOT NULL,
          email VARCHAR(255) DEFAULT NULL,
          tenant_id SMALLINT DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN operand.id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN operand.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('ALTER TABLE organization DROP email');
        $this->addSql('ALTER TABLE organization DROP contractor');
        $this->addSql('ALTER TABLE organization DROP seller');
        $this->addSql('ALTER TABLE organization DROP tenant_id');
        $this->addSql('ALTER TABLE
          organization
        ADD
          CONSTRAINT fk_c1ee637cbf396750 FOREIGN KEY (id) REFERENCES operand (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE person DROP email');
        $this->addSql('ALTER TABLE person DROP contractor');
        $this->addSql('ALTER TABLE person DROP seller');
        $this->addSql('ALTER TABLE person DROP tenant_id');
        $this->addSql('ALTER TABLE
          person
        ADD
          CONSTRAINT fk_34dcd176bf396750 FOREIGN KEY (id) REFERENCES operand (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
