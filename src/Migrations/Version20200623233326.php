<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200623233326 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE income ADD CONSTRAINT FK_3FA862D0748C73B5 FOREIGN KEY (accrued_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3FA862D0748C73B5 ON income (accrued_by_id)');
        $this->addSql('ALTER TABLE orders ALTER created_by_id SET NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEE1FA7797 FOREIGN KEY (closed_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E52FFDEEE1FA7797 ON orders (closed_by_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEB03A8386 ON orders (created_by_id)');
        $this->addSql('ALTER TABLE order_item ALTER created_by_id SET NOT NULL');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_52EA1F09B03A8386 ON order_item (created_by_id)');
        $this->addSql('ALTER TABLE order_note ALTER created_by_id SET NOT NULL');
        $this->addSql('ALTER TABLE order_note ADD CONSTRAINT FK_824CC003B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_824CC003B03A8386 ON order_note (created_by_id)');
        $this->addSql('ALTER TABLE order_payment ALTER created_by_id SET NOT NULL');
        $this->addSql('ALTER TABLE order_payment ADD CONSTRAINT FK_9B522D46B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9B522D46B03A8386 ON order_payment (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE income DROP CONSTRAINT FK_3FA862D0748C73B5');
        $this->addSql('DROP INDEX IDX_3FA862D0748C73B5');
        $this->addSql('ALTER TABLE order_payment DROP CONSTRAINT FK_9B522D46B03A8386');
        $this->addSql('DROP INDEX IDX_9B522D46B03A8386');
        $this->addSql('ALTER TABLE order_payment ALTER created_by_id DROP NOT NULL');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F09B03A8386');
        $this->addSql('DROP INDEX IDX_52EA1F09B03A8386');
        $this->addSql('ALTER TABLE order_item ALTER created_by_id DROP NOT NULL');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEEE1FA7797');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEEB03A8386');
        $this->addSql('DROP INDEX IDX_E52FFDEEE1FA7797');
        $this->addSql('DROP INDEX IDX_E52FFDEEB03A8386');
        $this->addSql('ALTER TABLE orders ALTER created_by_id DROP NOT NULL');
        $this->addSql('ALTER TABLE order_note DROP CONSTRAINT FK_824CC003B03A8386');
        $this->addSql('DROP INDEX IDX_824CC003B03A8386');
        $this->addSql('ALTER TABLE order_note ALTER created_by_id DROP NOT NULL');
    }
}
