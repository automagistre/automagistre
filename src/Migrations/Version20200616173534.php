<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200616173534 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('INSERT INTO migration_versions (version, executed_at) VALUES (\'20200616180432\', now())');

        $this->addSql('CREATE TABLE users (id SERIAL NOT NULL, uuid UUID NOT NULL, roles TEXT NOT NULL, username VARCHAR(255) NOT NULL, person_id UUID DEFAULT NULL, tenants JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9D17F50A6 ON users (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('COMMENT ON COLUMN users.uuid IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN users.roles IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN users.person_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('CREATE TABLE user_credentials (id SERIAL NOT NULL, user_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, payloads TEXT NOT NULL, expired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_531EE19BA76ED395 ON user_credentials (user_id)');
        $this->addSql('COMMENT ON COLUMN user_credentials.payloads IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN user_credentials.expired_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_credentials.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_credentials ADD CONSTRAINT FK_531EE19BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE user_credentials DROP CONSTRAINT FK_531EE19BA76ED395');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_credentials');
    }
}
