<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Part\Domain\PartCaseId;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function sprintf;
use function strpos;

final class Version20200524202031 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE part_case ADD id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN part_case.id IS \'(DC2Type:part_case_id)\'');
        // Data migration
        $empty = $this->connection->fetchAll('SELECT part_id, vehicle_id FROM part_case');
        foreach ($empty as $row) {
            $this->addSql(
                sprintf(
                    'UPDATE part_case SET id = \'%s\'::uuid WHERE part_id = \'%s\' AND vehicle_id = \'%s\'',
                    PartCaseId::generate()->toString(),
                    $row['part_id'],
                    $row['vehicle_id'],
                )
            );
        }
        // Data migration
        $this->addSql('ALTER TABLE part_case ALTER id SET NOT NULL');
        $this->addSql('ALTER TABLE part_case DROP CONSTRAINT part_case_pkey');
        $this->addSql('ALTER TABLE part_case ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('DROP INDEX part_case_pkey');
        $this->addSql('ALTER TABLE part_case DROP id');
        $this->addSql('ALTER TABLE part_case ADD PRIMARY KEY (part_id, vehicle_id)');
    }
}
