<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Appeal\Entity\AppealView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201218172710 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appeal_calculator ALTER id TYPE UUID USING id::uuid');
        $this->addSql('ALTER TABLE appeal_calculator ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('ALTER TABLE appeal_cooperation ALTER id TYPE UUID USING id::uuid');
        $this->addSql('ALTER TABLE appeal_cooperation ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_cooperation.id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('ALTER TABLE appeal_question ALTER id TYPE UUID USING id::uuid');
        $this->addSql('ALTER TABLE appeal_question ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_question.id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('ALTER TABLE appeal_schedule ALTER id TYPE UUID USING id::uuid');
        $this->addSql('ALTER TABLE appeal_schedule ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_schedule.id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER id TYPE UUID USING id::uuid');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.id IS \'(DC2Type:appeal_id)\'');

        $this->addSql('CREATE TABLE appeal_postpone (id UUID NOT NULL, appeal_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN appeal_postpone.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN appeal_postpone.appeal_id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('CREATE TABLE appeal_status (id UUID NOT NULL, appeal_id UUID NOT NULL, status SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN appeal_status.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN appeal_status.appeal_id IS \'(DC2Type:appeal_id)\'');
        $this->addSql('COMMENT ON COLUMN appeal_status.status IS \'(DC2Type:appeal_status)\'');

        $this->addSql(AppealView::sql());
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE appeal_postpone');
        $this->addSql('DROP TABLE appeal_status');

        $this->addSql('ALTER TABLE appeal_calculator ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE appeal_calculator ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_calculator.id IS NULL');
        $this->addSql('ALTER TABLE appeal_cooperation ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE appeal_cooperation ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_cooperation.id IS NULL');
        $this->addSql('ALTER TABLE appeal_question ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE appeal_question ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_question.id IS NULL');
        $this->addSql('ALTER TABLE appeal_schedule ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE appeal_schedule ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_schedule.id IS NULL');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN appeal_tire_fitting.id IS NULL');
    }
}
