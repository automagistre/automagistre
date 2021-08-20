<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210820175315 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_490f70c696901f54a23b42d');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_490F70C696901F54A23B42D9033212A ON part (number, manufacturer_id, tenant_id)');
        $this->addSql('DROP INDEX uniq_2a0e7894ce34bec545317d1');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A0E7894CE34BEC545317D19033212A ON part_case (part_id, vehicle_id, tenant_id)');
        $this->addSql('DROP INDEX uniq_794381c65f8a7f73953c1c61');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_794381C65F8A7F73953C1C619033212A ON review (source, source_id, tenant_id)');
        $this->addSql('DROP INDEX uniq_b53af235a23b42d5e237e06df3ba4b5');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B53AF235A23B42D5E237E06DF3BA4B59033212A ON vehicle_model (manufacturer_id, name, case_name, tenant_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_794381C65F8A7F73953C1C619033212A');
        $this->addSql('CREATE UNIQUE INDEX uniq_794381c65f8a7f73953c1c61 ON review (source, source_id)');
        $this->addSql('DROP INDEX UNIQ_490F70C696901F54A23B42D9033212A');
        $this->addSql('CREATE UNIQUE INDEX uniq_490f70c696901f54a23b42d ON part (number, manufacturer_id)');
        $this->addSql('DROP INDEX UNIQ_2A0E7894CE34BEC545317D19033212A');
        $this->addSql('CREATE UNIQUE INDEX uniq_2a0e7894ce34bec545317d1 ON part_case (part_id, vehicle_id)');
        $this->addSql('DROP INDEX UNIQ_B53AF235A23B42D5E237E06DF3BA4B59033212A');
        $this->addSql('CREATE UNIQUE INDEX uniq_b53af235a23b42d5e237e06df3ba4b5 ON vehicle_model (manufacturer_id, name, case_name)');
    }
}
