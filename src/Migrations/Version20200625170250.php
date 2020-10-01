<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200625170250 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT fk_52ea1f09b03a8386');
        $this->addSql('DROP INDEX idx_52ea1f09b03a8386');
        $this->addSql('ALTER TABLE order_item ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item ALTER uuid SET NOT NULL');
        $this->addSql('ALTER TABLE order_item DROP created_by_id');
        $this->addSql('ALTER TABLE order_item DROP created_at');
        $this->addSql('COMMENT ON COLUMN order_item.uuid IS \'(DC2Type:uuid)\'');

        $this->addSql('ALTER TABLE car_recommendation DROP created_at');
        $this->addSql('ALTER TABLE car_recommendation DROP created_by');
        $this->addSql('ALTER TABLE car_recommendation_part DROP created_at;');
        $this->addSql('ALTER TABLE car_recommendation_part DROP created_by');
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
