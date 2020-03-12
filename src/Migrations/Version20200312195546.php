<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200312195546 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'Landlord only');

        $this->addSql('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');

        $this->addSql('ALTER TABLE operand ADD uuid UUID DEFAULT NULL');
        $this->addSql('UPDATE operand SET uuid = uuid_generate_v4() WHERE uuid IS NULL');
        $this->addSql('COMMENT ON COLUMN operand.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83E03CE6D17F50A6 ON operand (uuid)');
        $this->addSql('ALTER TABLE operand ALTER uuid SET NOT NULL');
        $this->addSql('ALTER TABLE operand ALTER uuid DROP DEFAULT ');

        $this->addSql('ALTER TABLE part ADD uuid UUID DEFAULT NULL');
        $this->addSql('UPDATE part SET uuid = uuid_generate_v4() WHERE uuid IS NULL');
        $this->addSql('COMMENT ON COLUMN part.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_490F70C6D17F50A6 ON part (uuid)');
        $this->addSql('ALTER TABLE part ALTER COLUMN uuid SET NOT NULL');
        $this->addSql('ALTER TABLE part ALTER uuid DROP DEFAULT ');

        $this->addSql('ALTER TABLE car ADD uuid UUID DEFAULT NULL');
        $this->addSql('UPDATE car SET uuid = uuid_generate_v4() WHERE uuid IS NULL');
        $this->addSql('COMMENT ON COLUMN car.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69DD17F50A6 ON car (uuid)');
        $this->addSql('ALTER TABLE car ALTER COLUMN uuid SET NOT NULL');
        $this->addSql('ALTER TABLE car ALTER uuid DROP DEFAULT ');

        $this->addSql('ALTER TABLE car_model ADD uuid UUID DEFAULT NULL');
        $this->addSql('UPDATE car_model SET uuid = uuid_generate_v4() WHERE uuid IS NULL');
        $this->addSql('COMMENT ON COLUMN car_model.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83EF70ED17F50A6 ON car_model (uuid)');
        $this->addSql('ALTER TABLE car_model ALTER COLUMN uuid SET NOT NULL');
        $this->addSql('ALTER TABLE car_model ALTER uuid DROP DEFAULT ');

        $this->addSql('ALTER TABLE car_recommendation ADD uuid UUID DEFAULT NULL');
        $this->addSql('UPDATE car_recommendation SET uuid = uuid_generate_v4() WHERE uuid IS NULL');
        $this->addSql('COMMENT ON COLUMN car_recommendation.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8E4BAAF2D17F50A6 ON car_recommendation (uuid)');
        $this->addSql('ALTER TABLE car_recommendation ALTER COLUMN uuid SET NOT NULL');
        $this->addSql('ALTER TABLE car_recommendation ALTER uuid DROP DEFAULT ');

        $this->addSql('ALTER TABLE car_recommendation_part ADD uuid UUID DEFAULT NULL');
        $this->addSql('UPDATE car_recommendation_part SET uuid = uuid_generate_v4() WHERE uuid IS NULL');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DDC72D65D17F50A6 ON car_recommendation_part (uuid)');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER COLUMN uuid SET NOT NULL');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER uuid DROP DEFAULT ');

        $this->addSql('ALTER TABLE users ADD uuid UUID DEFAULT NULL');
        $this->addSql('UPDATE users SET uuid = uuid_generate_v4() WHERE uuid IS NULL');
        $this->addSql('COMMENT ON COLUMN users.uuid IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN users.tenants IS NULL');
        $this->addSql('ALTER TABLE users ALTER tenants TYPE JSON USING tenants::json');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9D17F50A6 ON users (uuid)');
        $this->addSql('ALTER TABLE users ALTER COLUMN uuid SET NOT NULL');
        $this->addSql('ALTER TABLE users ALTER uuid DROP DEFAULT ');

        $this->addSql('DROP EXTENSION IF EXISTS "uuid-ossp"');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'Landlord only');

        $this->addSql('DROP INDEX UNIQ_DDC72D65D17F50A6');
        $this->addSql('ALTER TABLE car_recommendation_part DROP uuid');
        $this->addSql('DROP INDEX UNIQ_83EF70ED17F50A6');
        $this->addSql('ALTER TABLE car_model DROP uuid');
        $this->addSql('DROP INDEX UNIQ_773DE69DD17F50A6');
        $this->addSql('ALTER TABLE car DROP uuid');
        $this->addSql('DROP INDEX UNIQ_83E03CE6D17F50A6');
        $this->addSql('ALTER TABLE operand DROP uuid');
        $this->addSql('DROP INDEX UNIQ_1483A5E9D17F50A6');
        $this->addSql('ALTER TABLE users DROP uuid');
        $this->addSql('COMMENT ON COLUMN users.tenants IS \'(DC2Type:json_array)\'');
        $this->addSql('DROP INDEX UNIQ_490F70C6D17F50A6');
        $this->addSql('ALTER TABLE part DROP uuid');
        $this->addSql('DROP INDEX UNIQ_8E4BAAF2D17F50A6');
        $this->addSql('ALTER TABLE car_recommendation DROP uuid');
    }
}
