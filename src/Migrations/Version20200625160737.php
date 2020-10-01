<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200625160737 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE order_payment DROP CONSTRAINT fk_9b522d46b03a8386');
        $this->addSql('DROP INDEX idx_9b522d46b03a8386');
        $this->addSql('ALTER TABLE order_payment ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE order_payment ALTER uuid SET NOT NULL');
        $this->addSql('ALTER TABLE order_payment DROP created_by_id');
        $this->addSql('ALTER TABLE order_payment DROP created_at');
        $this->addSql('COMMENT ON COLUMN order_payment.uuid IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE order_payment ADD created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_payment ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE order_payment DROP uuid');
        $this->addSql('COMMENT ON COLUMN order_payment.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE order_payment ADD CONSTRAINT fk_9b522d46b03a8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_9b522d46b03a8386 ON order_payment (created_by_id)');
    }
}
