<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function strpos;

final class Version20200516182701 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE orders ADD car_uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE orders DROP car_id');
        $this->addSql('ALTER TABLE orders RENAME car_uuid TO car_id');

        $this->addSql('ALTER TABLE orders ADD customer_uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE orders DROP customer_id');
        $this->addSql('ALTER TABLE orders RENAME customer_uuid TO customer_id');

        $this->addSql('COMMENT ON COLUMN orders.car_id IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN orders.customer_id IS \'(DC2Type:operand_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        throw new LogicException('Nope.');
    }
}
