<?php

declare(strict_types=1);

namespace App\Migrations;

use App\MC\Entity\McEquipmentId;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function sprintf;
use function strpos;

final class Version20200525172221 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE mc_equipment ADD uuid UUID DEFAULT NULL');
        // Data migration
        foreach ($this->connection->fetchAll('SELECT id FROM mc_equipment ORDER BY id') as $item) {
            $this->addSql(sprintf(
                'UPDATE mc_equipment SET uuid = \'%s\'::uuid WHERE id = %s',
                McEquipmentId::generate()->toString(),
                $item['id']
            ));
        }
        // Data migration
        $this->addSql('ALTER TABLE mc_equipment ALTER uuid SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN mc_equipment.uuid IS \'(DC2Type:mc_equipment_id)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('ALTER TABLE mc_equipment DROP uuid');
    }
}
