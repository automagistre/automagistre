<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210827080132 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE users SET username = LOWER(username) WHERE username <> LOWER(username)');
    }
}
