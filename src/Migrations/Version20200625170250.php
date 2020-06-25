<?php

declare(strict_types=1);

namespace App\Migrations;

use App\CreatedBy\Entity\CreatedByView;
use App\Employee\Entity\SalaryView;
use App\Note\Entity\NoteView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function implode;
use Ramsey\Uuid\Uuid;
use function sprintf;

final class Version20200625170250 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT fk_52ea1f09b03a8386');
        $this->addSql('DROP INDEX idx_52ea1f09b03a8386');
        $this->addSql('ALTER TABLE order_item ADD uuid UUID DEFAULT NULL');
        // data migration
        $values = [];
        foreach ($this->connection->fetchAll('SELECT id FROM order_item ORDER BY id') as $item) {
            $values[] = sprintf('(%s, \'%s\')', $item['id'], Uuid::uuid6()->toString());
        }

        if ([] !== $values) {
            $values = implode(',', $values);
            $this->addSql("UPDATE order_item SET uuid = v.uuid::uuid FROM (VALUES {$values}) v(id, uuid) WHERE order_item.id = v.id");
            $this->addSql('
                INSERT INTO created_by (id, user_id, created_at) 
                SELECT oi.uuid, u.uuid, oi.created_at
                FROM order_item oi
                    JOIN users u ON oi.created_by_id = u.id
        ');
        }
        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at) 
            SELECT cr.uuid, cr.created_by, cr.created_at
            FROM car_recommendation cr
        ');
        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at) 
            SELECT crp.uuid, crp.created_by, crp.created_at
            FROM car_recommendation_part crp
        ');
        // data migration
        $this->addSql('ALTER TABLE order_item ALTER uuid SET NOT NULL');
        $this->addSql('ALTER TABLE order_item DROP created_by_id');
        $this->addSql('ALTER TABLE order_item DROP created_at');
        $this->addSql('COMMENT ON COLUMN order_item.uuid IS \'(DC2Type:uuid)\'');

        $this->addSql('ALTER TABLE car_recommendation DROP created_at');
        $this->addSql('ALTER TABLE car_recommendation DROP created_by');
        $this->addSql('ALTER TABLE car_recommendation_part DROP created_at;');
        $this->addSql('ALTER TABLE car_recommendation_part DROP created_by');

        $this->addSql('DROP VIEW IF EXISTS note_view');
        $this->addSql(NoteView::sql());

        $this->addSql('DROP VIEW IF EXISTS salary_view');
        $this->addSql(SalaryView::sql());
        $this->addSql(CreatedByView::sql());
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE order_item ADD created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_item ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE order_item DROP uuid');
        $this->addSql('COMMENT ON COLUMN order_item.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT fk_52ea1f09b03a8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_52ea1f09b03a8386 ON order_item (created_by_id)');
    }
}
