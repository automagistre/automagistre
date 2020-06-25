<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;
use function sprintf;

final class Version20200625192755 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE review ADD uuid UUID DEFAULT NULL');
        // data migration
        foreach ($this->connection->fetchAll('SELECT id FROM review ORDER BY id') as $item) {
            $this->addSql(sprintf(
                'UPDATE review SET uuid = \'%s\'::uuid WHERE id = %s',
                Uuid::uuid6()->toString(),
                $item['id'],
            ));
        }
        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at) 
            SELECT r.uuid, \'4ffc24e2-8e60-42e0-9c8f-7a73888b2da6\'::uuid, r.created_at
            FROM review r
        ');
        // data migration
        $this->addSql('ALTER TABLE review DROP id');
        $this->addSql('ALTER TABLE review RENAME uuid TO id');
        $this->addSql('ALTER TABLE review ALTER id SET NOT NULL ');
        $this->addSql('ALTER TABLE review ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE review DROP created_at');
        $this->addSql('COMMENT ON COLUMN review.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE review_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE review ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE review ALTER id TYPE INT');
        $this->addSql('ALTER TABLE review ALTER id DROP DEFAULT');
        $this->addSql('CREATE SEQUENCE review_id_seq');
        $this->addSql('SELECT setval(\'review_id_seq\', (SELECT MAX(id) FROM review))');
        $this->addSql('ALTER TABLE review ALTER id SET DEFAULT nextval(\'review_id_seq\')');
        $this->addSql('COMMENT ON COLUMN review.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN review.id IS NULL');
    }
}
