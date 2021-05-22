<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210522105004 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE order_item_part SET discount_amount = 0 WHERE discount_amount IS NULL');
        $this->addSql('UPDATE order_item_part SET discount_currency_code = \'RUB\' WHERE discount_currency_code IS NULL');

        $this->addSql('UPDATE order_item_service SET discount_amount = 0 WHERE discount_amount IS NULL');
        $this->addSql('UPDATE order_item_service SET discount_currency_code = \'RUB\' WHERE discount_currency_code IS NULL');
    }
}
