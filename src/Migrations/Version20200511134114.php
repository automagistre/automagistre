<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Order\Entity\OrderId;
use function array_map;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function sprintf;
use function strpos;

final class Version20200511134114 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE orders ADD uuid UUID DEFAULT NULL');

        $ids = $this->connection->fetchAll('SELECT id FROM orders');
        /** @var int[] $ids */
        $ids = array_map('array_shift', $ids);
        foreach ($ids as $id) {
            $this->addSql(
                sprintf(
                    'UPDATE orders SET uuid = \'%s\'::uuid WHERE id = %s',
                    OrderId::generate()->toString(),
                    $id
                )
            );
        }

        $this->addSql('ALTER TABLE orders ALTER uuid SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN orders.uuid IS \'(DC2Type:order_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        throw new LogicException('Nope.');
    }
}
