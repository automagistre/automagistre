<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200616211944 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE users_password (id UUID NOT NULL, user_id INT DEFAULT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4E836D0FA76ED395 ON users_password (user_id)');
        $this->addSql('COMMENT ON COLUMN users_password.id IS \'(DC2Type:user_password_id)\'');
        $this->addSql('ALTER TABLE users_password ADD CONSTRAINT FK_D54FA2D5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('DROP TABLE user_credentials');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE user_credentials (id UUID NOT NULL, user_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, payloads TEXT NOT NULL, expired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_531ee19ba76ed395 ON user_credentials (user_id)');
        $this->addSql('COMMENT ON COLUMN user_credentials.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_credentials.payloads IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN user_credentials.expired_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_credentials ADD CONSTRAINT fk_531ee19ba76ed395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE users_password');
    }
}
