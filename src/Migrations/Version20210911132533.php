<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210911132533 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE manufacturer DROP tenant_id');
        $this->addSql('DROP INDEX uniq_490f70c696901f54a23b42d9033212a');
        $this->addSql('ALTER TABLE part DROP tenant_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_490F70C696901F54A23B42D ON part (number, manufacturer_id)');
        $this->addSql('DROP INDEX uniq_b53af235a23b42d5e237e06df3ba4b59033212a');
        $this->addSql('ALTER TABLE vehicle_model DROP tenant_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B53AF235A23B42D5E237E06DF3BA4B5 ON vehicle_model (manufacturer_id, name, case_name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE manufacturer ADD tenant_id SMALLINT NOT NULL');
        $this->addSql('COMMENT ON COLUMN manufacturer.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('DROP INDEX UNIQ_B53AF235A23B42D5E237E06DF3BA4B5');
        $this->addSql('ALTER TABLE vehicle_model ADD tenant_id SMALLINT NOT NULL');
        $this->addSql('COMMENT ON COLUMN vehicle_model.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE UNIQUE INDEX uniq_b53af235a23b42d5e237e06df3ba4b59033212a ON vehicle_model (
          manufacturer_id, name, case_name,
          tenant_id
        )');
        $this->addSql('DROP INDEX UNIQ_490F70C696901F54A23B42D');
        $this->addSql('ALTER TABLE part ADD tenant_id SMALLINT NOT NULL');
        $this->addSql('COMMENT ON COLUMN part.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('CREATE UNIQUE INDEX uniq_490f70c696901f54a23b42d9033212a ON part (
          number, manufacturer_id, tenant_id
        )');
    }
}
