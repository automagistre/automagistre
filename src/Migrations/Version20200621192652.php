<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Part\Entity\PartView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200621192652 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE INDEX IDX_59BB753A4CE34BEC ON part_price (part_id)');
        $this->addSql('CREATE INDEX IDX_76B231714CE34BEC ON part_discount (part_id)');
        $this->addSql(PartView::sql());
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX IDX_76B231714CE34BEC');
        $this->addSql('DROP INDEX IDX_59BB753A4CE34BEC');
    }
}
