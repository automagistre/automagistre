<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function strpos;

final class Version20200512171048 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE car_recommendation ADD created_by UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE car_recommendation_part ADD created_by UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE car_recommendation DROP created_by_id');
        $this->addSql('ALTER TABLE car_recommendation_part DROP created_by_id');
        $this->addSql('ALTER TABLE car_recommendation ALTER created_by SET NOT NULL ');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER created_by SET NOT NULL');
        $this->addSql('ALTER TABLE car_recommendation_part ADD part_uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE car_recommendation_part DROP part_id');
        $this->addSql('ALTER TABLE car_recommendation_part RENAME part_uuid TO part_id');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER part_id SET NOT NULL');
        $this->addSql('ALTER TABLE car_recommendation ADD worker_uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE car_recommendation DROP worker_id');
        $this->addSql('ALTER TABLE car_recommendation RENAME worker_uuid TO worker_id');
        $this->addSql('ALTER TABLE car_recommendation ALTER worker_id SET NOT NULL');

        $this->addSql('COMMENT ON COLUMN car_recommendation.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.worker_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.part_id IS \'(DC2Type:part_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        throw new LogicException('Fiasco brat.');
    }
}
