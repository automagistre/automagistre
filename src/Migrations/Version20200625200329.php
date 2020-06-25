<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function implode;
use Ramsey\Uuid\Uuid;
use function sprintf;

final class Version20200625200329 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE motion ADD uuid UUID DEFAULT NULL');
        // data migration
        $values = [];
        foreach ($this->connection->fetchAll('SELECT id FROM motion ORDER BY id') as $item) {
            $values[] = sprintf('(%s, \'%s\')', $item['id'], Uuid::uuid6()->toString());
        }

        if ([] !== $values) {
            $values = implode(',', $values);
            $this->addSql("UPDATE motion SET uuid = v.uuid::uuid FROM (VALUES {$values}) v(id, uuid) WHERE motion.id = v.id");
        }
        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at) 
            SELECT m.uuid, \'4ffc24e2-8e60-42e0-9c8f-7a73888b2da6\'::uuid, m.created_at
            FROM motion m
        ');
        // data migration
        $this->addSql('DROP INDEX idx_f5fea1e84ce34bec8b8e8428');
        $this->addSql('ALTER TABLE motion DROP created_at');
        $this->addSql('ALTER TABLE motion DROP id');
        $this->addSql('ALTER TABLE motion RENAME uuid TO id');
        $this->addSql('ALTER TABLE motion ALTER id SET NOT NULL');
        $this->addSql('ALTER TABLE motion ADD PRIMARY KEY (id)');
        $this->addSql('COMMENT ON COLUMN motion.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE motion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE motion ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE motion ALTER id TYPE INT');
        $this->addSql('ALTER TABLE motion ALTER id DROP DEFAULT');
        $this->addSql('CREATE SEQUENCE motion_id_seq');
        $this->addSql('SELECT setval(\'motion_id_seq\', (SELECT MAX(id) FROM motion))');
        $this->addSql('ALTER TABLE motion ALTER id SET DEFAULT nextval(\'motion_id_seq\')');
        $this->addSql('COMMENT ON COLUMN motion.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN motion.id IS NULL');
        $this->addSql('CREATE INDEX idx_f5fea1e84ce34bec8b8e8428 ON motion (part_id, created_at)');
    }
}
