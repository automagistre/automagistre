<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Customer\Entity\CustomerView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201011211258 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(CustomerView::sql());
    }

    public function down(Schema $schema): void
    {
    }
}
