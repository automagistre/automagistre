<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210830185945 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE users_password');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users_password (
          id UUID NOT NULL,
          user_id UUID DEFAULT NULL,
          password VARCHAR(255) NOT NULL,
          tenant_id SMALLINT DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_4e836d0fa76ed395 ON users_password (user_id)');
        $this->addSql('COMMENT ON COLUMN users_password.id IS \'(DC2Type:user_password_id)\'');
        $this->addSql('COMMENT ON COLUMN users_password.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN users_password.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('ALTER TABLE
          users_password
        ADD
          CONSTRAINT fk_4e836d0fa76ed395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
