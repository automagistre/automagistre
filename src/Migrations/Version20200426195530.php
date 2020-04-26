<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200426195530 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE car_recommendation ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE car_recommendation ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car_recommendation.uuid IS \'(DC2Type:recommendation_id)\'');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.uuid IS \'(DC2Type:recommendation_part_id)\'');
        $this->addSql('ALTER TABLE operand ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE operand ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN operand.uuid IS \'(DC2Type:operand_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE car_recommendation_part ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE operand ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE operand ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN operand.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE car_recommendation ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE car_recommendation ALTER uuid DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car_recommendation.uuid IS \'(DC2Type:uuid)\'');
    }
}
