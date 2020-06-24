<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Note\Entity\NoteView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;
use function sprintf;

final class Version20200624171043 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE car_note_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE operand_note_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE order_note_id_seq CASCADE');
        $this->addSql('CREATE TABLE note (id UUID NOT NULL, subject UUID NOT NULL, type SMALLINT NOT NULL, text TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN note.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN note.subject IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN note.type IS \'(DC2Type:note_type_enum)\'');

        // data migration
        foreach ($this->connection->fetchAll('
            SELECT *
            FROM (
                     SELECT c2.uuid      AS subject,
                            n.type       AS type,
                            n.text       AS text,
                            n.created_by AS created_by,
                            n.created_at AS created_at
                     FROM car_note n
                              JOIN car c2 ON n.car_id = c2.id
                     UNION ALL
                     SELECT o.uuid       AS subject,
                            n.type       AS type,
                            n.text       AS text,
                            n.created_by AS created_by,
                            n.created_at AS created_at
                     FROM operand_note n
                              JOIN operand o ON n.operand_id = o.id                       
                     UNION ALL
                     SELECT o2.uuid      AS subject,
                            n.type       AS type,
                            n.text       AS text,
                            u.uuid       AS created_by,
                            n.created_at AS created_at
                     FROM order_note n
                            JOIN orders o2 ON n.order_id = o2.id
                            JOIN users u ON n.created_by_id = u.id
                 ) sub
            ORDER BY sub.created_at
        ') as $row) {
            $uuid = Uuid::uuid6()->toString();
            $this->addSql(sprintf(
                'INSERT INTO note (id, subject, type, text) VALUES (\'%s\'::UUID, \'%s\'::UUID, %s, \'%s\')',
                $uuid,
                $row['subject'],
                $row['type'],
                $row['text'],
            ));
            $this->addSql(sprintf(
                'INSERT INTO db.public.created_by (id, user_id, created_at) VALUES (\'%s\'::UUID, \'%s\'::UUID, \'%s\')',
                $uuid,
                $row['created_by'],
                $row['created_at']
            ));
        }
        // data migration

        $this->addSql('DROP TABLE car_note');
        $this->addSql('DROP TABLE operand_note');
        $this->addSql('DROP TABLE order_note');

        $this->addSql(NoteView::sql());
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE car_note_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE operand_note_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE order_note_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE car_note (id SERIAL NOT NULL, car_id INT DEFAULT NULL, type SMALLINT NOT NULL, text TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_4d7eeb8c3c6f69f ON car_note (car_id)');
        $this->addSql('COMMENT ON COLUMN car_note.type IS \'(DC2Type:note_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN car_note.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car_note.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE operand_note (id SERIAL NOT NULL, operand_id INT DEFAULT NULL, type SMALLINT NOT NULL, text TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_36bde44118d7f226 ON operand_note (operand_id)');
        $this->addSql('COMMENT ON COLUMN operand_note.type IS \'(DC2Type:note_type_enum)\'');
        $this->addSql('COMMENT ON COLUMN operand_note.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN operand_note.created_by IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE order_note (id SERIAL NOT NULL, order_id INT DEFAULT NULL, created_by_id INT NOT NULL, text TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_824cc003b03a8386 ON order_note (created_by_id)');
        $this->addSql('CREATE INDEX idx_824cc0038d9f6d38 ON order_note (order_id)');
        $this->addSql('COMMENT ON COLUMN order_note.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN order_note.type IS \'(DC2Type:note_type_enum)\'');
        $this->addSql('ALTER TABLE car_note ADD CONSTRAINT fk_4d7eeb8c3c6f69f FOREIGN KEY (car_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE operand_note ADD CONSTRAINT fk_36bde44118d7f226 FOREIGN KEY (operand_id) REFERENCES operand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_note ADD CONSTRAINT fk_cfbdfa148d9f6d38 FOREIGN KEY (order_id) REFERENCES orders (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_note ADD CONSTRAINT fk_824cc003b03a8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE note');
    }
}
