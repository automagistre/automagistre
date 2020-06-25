<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Employee\Entity\SalaryView;
use App\Note\Entity\NoteView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200625165240 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP VIEW IF EXISTS note_view');
        $this->addSql(NoteView::sql());

        $this->addSql('DROP VIEW IF EXISTS salary_view');
        $this->addSql(SalaryView::sql());
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
    }
}
