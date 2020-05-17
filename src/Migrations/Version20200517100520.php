<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200517100520 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('DROP TABLE car_possession');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('CREATE TABLE car_possession (
          id UUID NOT NULL, 
          possessor_id UUID NOT NULL, 
          car_id UUID NOT NULL, 
          transition SMALLINT NOT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN car_possession.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN car_possession.possessor_id IS \'(DC2Type:operand_id)\'');
        $this->addSql('COMMENT ON COLUMN car_possession.car_id IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN car_possession.transition IS \'(DC2Type:transition_enum)\'');
    }
}
