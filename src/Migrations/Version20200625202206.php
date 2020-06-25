<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;
use function sprintf;

final class Version20200625202206 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE mc_work ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE mc_line ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE mc_part ADD uuid UUID DEFAULT NULL');
        // data migration
        foreach ($this->connection->fetchAll('SELECT id FROM mc_work ORDER BY id') as $item) {
            $this->addSql(sprintf(
                'UPDATE mc_work SET uuid = \'%s\'::uuid WHERE id = %s',
                Uuid::uuid6()->toString(),
                $item['id'],
            ));
        }

        foreach ($this->connection->fetchAll('SELECT id FROM mc_line ORDER BY id') as $item) {
            $this->addSql(sprintf(
                'UPDATE mc_line SET uuid = \'%s\'::uuid WHERE id = %s',
                Uuid::uuid6()->toString(),
                $item['id'],
            ));
        }

        foreach ($this->connection->fetchAll('SELECT id FROM mc_part ORDER BY id') as $item) {
            $this->addSql(sprintf(
                'UPDATE mc_part SET uuid = \'%s\'::uuid WHERE id = %s',
                Uuid::uuid6()->toString(),
                $item['id'],
            ));
        }
        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at) 
            SELECT w.uuid, \'4ffc24e2-8e60-42e0-9c8f-7a73888b2da6\'::uuid, now()
            FROM mc_work w
        ');
        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at) 
            SELECT l.uuid, \'4ffc24e2-8e60-42e0-9c8f-7a73888b2da6\'::uuid, now()
            FROM mc_line l
        ');
        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at) 
            SELECT p.uuid, \'4ffc24e2-8e60-42e0-9c8f-7a73888b2da6\'::uuid, now()
            FROM mc_part p
        ');
        // data migration
        $this->addSql('ALTER TABLE mc_work ALTER uuid SET NOT NULL');
        $this->addSql('ALTER TABLE mc_line ALTER uuid SET NOT NULL');
        $this->addSql('ALTER TABLE mc_part ALTER uuid SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN mc_work.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mc_line.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mc_part.uuid IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE mc_work DROP uuid');
        $this->addSql('ALTER TABLE mc_line DROP uuid');
        $this->addSql('ALTER TABLE mc_part DROP uuid');
    }
}
