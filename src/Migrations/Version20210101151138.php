<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210101151138 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE google_review_token (id UUID NOT NULL, expire_id UUID DEFAULT NULL, payload JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8DF201415A317C65 ON google_review_token (expire_id)');
        $this->addSql('COMMENT ON COLUMN google_review_token.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN google_review_token.expire_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE google_review_token_expire (id UUID NOT NULL, token_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D1656DE441DEE7B9 ON google_review_token_expire (token_id)');
        $this->addSql('COMMENT ON COLUMN google_review_token_expire.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN google_review_token_expire.token_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE google_review_token ADD CONSTRAINT UNIQ_8DF201415A317C65 FOREIGN KEY (expire_id) REFERENCES google_review_token_expire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE google_review_token_expire ADD CONSTRAINT FK_2DC9E5D641DEE7B9 FOREIGN KEY (token_id) REFERENCES google_review_token (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE TABLE google_review (id UUID NOT NULL, review_id VARCHAR(255) NOT NULL, payload JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN google_review.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE google_review_token_expire DROP CONSTRAINT FK_2DC9E5D641DEE7B9');
        $this->addSql('ALTER TABLE google_review_token DROP CONSTRAINT FK_5F37A13B5A317C65');
        $this->addSql('DROP TABLE google_review');
    }
}
