<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function strpos;

final class Version20200511183133 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE motion ADD source SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE motion ADD source_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN motion.source IS \'(DC2Type:motion_source_enum)\'');
        $this->addSql('COMMENT ON COLUMN motion.source_id IS \'(DC2Type:uuid)\'');

        $this->addSql('ALTER TABLE income_part DROP CONSTRAINT fk_834566e8ffe2c7');
        $this->addSql('ALTER TABLE income_part DROP accrued_motion_id');

        $this->addSql('DROP TABLE motion_income');
        $this->addSql('DROP TABLE motion_order');
        $this->addSql('DROP TABLE motion_manual');
        $this->addSql('DROP TABLE motion_old');
        $this->addSql('ALTER TABLE orders ALTER uuid SET NOT NULL');
        $this->addSql('ALTER TABLE motion DROP type');

        $this->addSql('ALTER TABLE motion ALTER source SET NOT NULL');
        $this->addSql('ALTER TABLE motion ALTER source_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        throw new LogicException('Nope.');
    }
}
