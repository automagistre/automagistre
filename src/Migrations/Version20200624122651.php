<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;
use function sprintf;

final class Version20200624122651 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE order_contractor_id_seq CASCADE');
        $this->addSql('ALTER TABLE order_contractor DROP CONSTRAINT fk_f0a12fba8d9f6d38');
        $this->addSql('DROP INDEX idx_f0a12fba8d9f6d38');
        $this->addSql('ALTER TABLE order_contractor ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE order_contractor ADD order_uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE order_contractor ADD operand_id UUID DEFAULT NULL');

        // data migration
        foreach ($this->connection->fetchAll('SELECT id FROM order_contractor ORDER BY id') as $row) {
            $this->addSql(sprintf(
                'UPDATE order_contractor SET uuid = \'%s\'::uuid WHERE id = %s',
                Uuid::uuid6()->toString(),
                $row['id'],
            ));
        }
        $this->addSql('UPDATE order_contractor SET operand_id = sub.uuid FROM (select id, uuid FROM operand) sub WHERE sub.id = order_contractor.contractor_id');
        $this->addSql('UPDATE order_contractor SET order_uuid = sub.uuid FROM (select id, uuid FROM orders) sub WHERE sub.id = order_contractor.order_id');
        // data migration

        $this->addSql('ALTER TABLE order_contractor DROP contractor_id');
        $this->addSql('ALTER TABLE order_contractor DROP id');
        $this->addSql('ALTER TABLE order_contractor DROP order_id');
        $this->addSql('ALTER TABLE order_contractor RENAME uuid TO id');
        $this->addSql('ALTER TABLE order_contractor RENAME order_uuid TO order_id');
        $this->addSql('ALTER TABLE order_contractor ALTER order_id DROP DEFAULT');
        $this->addSql('ALTER TABLE order_contractor ALTER order_id SET NOT NULL');
        $this->addSql('ALTER TABLE order_contractor ALTER id SET NOT NULL');
        $this->addSql('ALTER TABLE order_contractor ALTER operand_id SET NOT NULL');
        $this->addSql('ALTER TABLE order_contractor ADD PRIMARY KEY (id)');
        $this->addSql('COMMENT ON COLUMN order_contractor.operand_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN order_contractor.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_contractor.order_id IS \'(DC2Type:order_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE order_contractor_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE order_contractor ADD contractor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_contractor DROP operand_id');
        $this->addSql('ALTER TABLE order_contractor ALTER id TYPE INT');
        $this->addSql('ALTER TABLE order_contractor ALTER id DROP DEFAULT');
        $this->addSql('CREATE SEQUENCE order_contractor_id_seq');
        $this->addSql('SELECT setval(\'order_contractor_id_seq\', (SELECT MAX(id) FROM order_contractor))');
        $this->addSql('ALTER TABLE order_contractor ALTER id SET DEFAULT nextval(\'order_contractor_id_seq\')');
        $this->addSql('ALTER TABLE order_contractor ALTER order_id TYPE INT');
        $this->addSql('ALTER TABLE order_contractor ALTER order_id DROP DEFAULT');
        $this->addSql('ALTER TABLE order_contractor ALTER order_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN order_contractor.id IS NULL');
        $this->addSql('COMMENT ON COLUMN order_contractor.order_id IS NULL');
        $this->addSql('ALTER TABLE order_contractor ADD CONSTRAINT fk_f0a12fba8d9f6d38 FOREIGN KEY (order_id) REFERENCES orders (id) ON UPDATE RESTRICT ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_f0a12fba8d9f6d38 ON order_contractor (order_id)');
    }
}
