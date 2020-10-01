<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200623223607 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE order_suspend ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE order_suspend ADD order_uuid UUID DEFAULT NULL');

        $this->addSql('DROP SEQUENCE order_suspend_id_seq CASCADE');
        $this->addSql('ALTER TABLE order_suspend DROP id');
        $this->addSql('ALTER TABLE order_suspend RENAME uuid to id');
        $this->addSql('ALTER TABLE order_suspend ALTER id SET NOT NULL');
        $this->addSql('ALTER TABLE order_suspend ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE order_suspend ALTER order_uuid SET NOT NULL');
        $this->addSql('ALTER TABLE order_suspend DROP created_at');
        $this->addSql('ALTER TABLE order_suspend DROP created_by_id');
        $this->addSql('ALTER TABLE order_suspend ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN order_suspend.order_uuid IS \'(DC2Type:order_id)\'');
        $this->addSql('COMMENT ON COLUMN order_suspend.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE order_suspend_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE order_suspend ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE order_suspend ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_suspend DROP order_uuid');
        $this->addSql('ALTER TABLE order_suspend ALTER id TYPE INT');
        $this->addSql('ALTER TABLE order_suspend ALTER id DROP DEFAULT');
        $this->addSql('CREATE SEQUENCE order_suspend_id_seq');
        $this->addSql('SELECT setval(\'order_suspend_id_seq\', (SELECT MAX(id) FROM order_suspend))');
        $this->addSql('ALTER TABLE order_suspend ALTER id SET DEFAULT nextval(\'order_suspend_id_seq\')');
        $this->addSql('COMMENT ON COLUMN order_suspend.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN order_suspend.id IS NULL');
    }
}
