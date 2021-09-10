<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210910212225 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE order_deal SET balance = \'RUB 0\' WHERE balance IS NULL');

        foreach ([
            'order_deal' => 'balance',
            'appeal_calculator' => 'total',
            'appeal_tire_fitting' => 'total',
            'employee_salary' => 'amount',
        ] as $table => $column) {
            $this->addSql("ALTER TABLE {$table} ADD tmp BIGINT DEFAULT NULL");
            $this->addSql("UPDATE {$table} SET tmp = replace({$column}, 'RUB ', '')::BIGINT");
            $this->addSql("ALTER TABLE {$table} DROP {$column}");
            $this->addSql("ALTER TABLE {$table} RENAME tmp TO {$column}");
            $this->addSql("ALTER TABLE {$table} ALTER {$column} DROP DEFAULT");
            $this->addSql("ALTER TABLE {$table} ALTER {$column} SET NOT NULL");
            $this->addSql("COMMENT ON COLUMN {$table}.{$column} IS '(DC2Type:money)';");
        }
    }
}
