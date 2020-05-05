<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200427004901 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE part_case DROP CONSTRAINT fk_2a0e7894ce34bec');
        $this->addSql('ALTER TABLE part_case DROP CONSTRAINT fk_2a0e789f64382e3');
        $this->addSql('DROP INDEX uniq_2a0e7894ce34becf64382e3');
        $this->addSql('DROP INDEX idx_2a0e789f64382e3');
        $this->addSql('DROP INDEX idx_2a0e7894ce34bec');

        $this->addSql('ALTER TABLE part_case ALTER car_model_id TYPE VARCHAR');
        $this->addSql('UPDATE part_case t SET car_model_id = v.uuid FROM (SELECT id, uuid FROM car_model) AS v WHERE t.car_model_id::int = v.id');
        $this->addSql('ALTER TABLE part_case RENAME car_model_id TO vehicle_id');
        $this->addSql('ALTER TABLE part_case ALTER vehicle_id TYPE UUID USING vehicle_id::uuid');
        $this->addSql('ALTER TABLE part_case ALTER vehicle_id SET NOT NULL');

        $this->addSql('ALTER TABLE part_case ALTER part_id TYPE VARCHAR');
        $this->addSql('UPDATE part_case t SET part_id = v.uuid FROM (SELECT id, part_id AS uuid FROM part) AS v WHERE t.part_id::int = v.id');
        $this->addSql('ALTER TABLE part_case ALTER part_id TYPE UUID USING part_id::uuid');
        $this->addSql('ALTER TABLE part_case ALTER part_id DROP DEFAULT');
        $this->addSql('ALTER TABLE part_case ALTER part_id SET NOT NULL');

        $this->addSql('COMMENT ON COLUMN part_case.vehicle_id IS \'(DC2Type:vehicle_id)\'');
        $this->addSql('COMMENT ON COLUMN part_case.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A0E7894CE34BEC545317D1 ON part_case (part_id, vehicle_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('DROP INDEX UNIQ_2A0E7894CE34BEC545317D1');
        $this->addSql('ALTER TABLE part_case ADD car_model_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE part_case DROP vehicle_id');
        $this->addSql('ALTER TABLE part_case ALTER part_id TYPE INT');
        $this->addSql('ALTER TABLE part_case ALTER part_id DROP DEFAULT');
        $this->addSql('ALTER TABLE part_case ALTER part_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN part_case.part_id IS NULL');
        $this->addSql('ALTER TABLE 
          part_case 
        ADD 
          CONSTRAINT fk_2a0e7894ce34bec FOREIGN KEY (part_id) REFERENCES part (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE 
          part_case 
        ADD 
          CONSTRAINT fk_2a0e789f64382e3 FOREIGN KEY (car_model_id) REFERENCES car_model (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_2a0e7894ce34becf64382e3 ON part_case (part_id, car_model_id)');
        $this->addSql('CREATE INDEX idx_2a0e789f64382e3 ON part_case (car_model_id)');
        $this->addSql('CREATE INDEX idx_2a0e7894ce34bec ON part_case (part_id)');
    }
}
