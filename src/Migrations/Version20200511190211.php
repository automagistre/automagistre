<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function strpos;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20200511190211 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE income_part ADD part_uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item_part ADD part_uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE income_part DROP part_id');
        $this->addSql('ALTER TABLE income_part RENAME part_uuid TO part_id');
        $this->addSql('ALTER TABLE income_part ALTER part_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN income_part.part_id IS \'(DC2Type:part_id)\'');

        $this->addSql('ALTER TABLE order_item_part ALTER part_uuid SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN order_item_part.part_uuid IS \'(DC2Type:part_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        throw new LogicException('Nope.');
    }
}
