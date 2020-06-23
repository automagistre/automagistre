<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200623231240 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('INSERT INTO users (id, uuid, roles, username, first_name, last_name) VALUES (9, \'59861141-83b2-416c-b672-8ba8a1cb76b2\', \'[]\', \'service@automagistre.ru\', null, null) ON CONFLICT DO NOTHING');

        $this->addSql('UPDATE created_by SET user_id = (SELECT uuid FROM users WHERE username = \'service@automagistre.ru\') WHERE user_id IS NULL');
        $this->addSql('ALTER TABLE created_by ALTER user_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE created_by ALTER user_id DROP NOT NULL');
    }
}
