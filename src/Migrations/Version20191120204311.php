<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191120204311 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->skipIf('landlord' !== $this->connection->getDatabase(), 'Landlord only');

        $this->addSql('UPDATE car SET description = CONCAT_WS(\'\', description, IF(description IS NULL, \'\', \'\n\'), \'VIN: \', vin) WHERE vin IS NOT NULL AND length(vin) <> 17');
        $this->addSql('UPDATE car SET vin = null WHERE vin IS NOT NULL AND length(vin) <> 17');
        $this->addSql('UPDATE car SET description = NULL WHERE trim(description) = \'\'');
    }

    public function down(Schema $schema): void
    {
    }
}
