<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200505133632 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');

        $this->addSql('UPDATE part SET price_amount = 0, price_currency_code = \'RUB\' WHERE price_amount IS NULL');
        $this->addSql('UPDATE part SET discount_amount = 0, discount_currency_code = \'RUB\' WHERE discount_amount IS NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'landlord only');
    }
}
