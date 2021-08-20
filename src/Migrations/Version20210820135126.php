<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Tenant\Enum\Tenant;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function getenv;

final class Version20210820135126 extends AbstractMigration
{
    private const TENANT_TABLES = [
        'appeal_calculator',
        'appeal_call',
        'appeal_cooperation',
        'appeal_postpone',
        'appeal_question',
        'appeal_schedule',
        'appeal_status',
        'appeal_tire_fitting',
        'calendar_entry',
        'calendar_entry_deletion',
        'calendar_entry_order',
        'calendar_entry_order_info',
        'calendar_entry_schedule',
        'car',
        'car_recommendation',
        'car_recommendation_part',
        'created_by',
        'customer_transaction',
        'employee',
        'employee_salary',
        'employee_salary_end',
        'expense',
        'google_review_token',
        'income',
        'income_part',
        'inventorization',
        'inventorization_close',
        'inventorization_part',
        'manufacturer',
        'mc_equipment',
        'mc_line',
        'mc_part',
        'mc_work',
        'motion',
        'note',
        'note_delete',
        'operand',
        'order_close',
        'order_item',
        'order_payment',
        'order_suspend',
        'orders',
        'part',
        'part_case',
        'part_cross',
        'part_discount',
        'part_price',
        'part_required_availability',
        'part_supply',
        'publish',
        'reservation',
        'review',
        'sms',
        'sms_send',
        'sms_status',
        'storage_part',
        'users',
        'users_password',
        'vehicle_model',
        'wallet',
        'wallet_transaction',
        'warehouse',
        'warehouse_code',
        'warehouse_name',
        'warehouse_parent',
    ];

    public function up(Schema $schema): void
    {
        $tenantIdentifier = getenv('TENANT');

        if (false === $tenantIdentifier) {
            throw new LogicException('TENANT env must be defined');
        }
        $tenant = Tenant::fromIdentifier($tenantIdentifier);
        $currentTenant = $tenant->toId();

        foreach (self::TENANT_TABLES as $table) {
            $this->addSql("ALTER TABLE {$table} ADD tenant_id SMALLINT DEFAULT NULL");
            $this->addSql("COMMENT ON COLUMN {$table}.tenant_id IS '(DC2Type:tenant_enum)'");
            $this->addSql("UPDATE {$table} SET tenant_id = {$currentTenant}");
            $this->addSql("ALTER TABLE {$table} ALTER tenant_id DROP NOT NULL");
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::TENANT_TABLES as $table) {
            $this->addSql("ALTER TABLE {$table} DROP tenant_id");
        }
    }
}
