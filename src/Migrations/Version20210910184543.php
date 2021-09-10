<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210910184543 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appeal_calculator ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE appeal_call ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE appeal_cooperation ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE appeal_postpone ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE appeal_question ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE appeal_schedule ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE appeal_status ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry_deletion ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry_order ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry_order_info ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry_schedule ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE car ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE car_recommendation ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE created_by ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE customer_transaction ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE employee ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE employee_salary ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE employee_salary_end ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE expense ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE google_review_token ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE income ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE income_accrue ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE income_part ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE inventorization ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE inventorization_close ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE inventorization_part ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE manufacturer ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE mc_equipment ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE mc_line ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE mc_part ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE mc_work ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE motion ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE note ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE note_delete ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE order_close ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE order_item ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE order_payment ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE order_suspend ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE orders ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE organization ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE part ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE part_case ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE part_cross ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE part_discount ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE part_price ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE part_required_availability ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE part_supply ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE person ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE publish ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE reservation ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE review ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE sms ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE sms_send ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE storage_part ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE user_permission ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE vehicle_model ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE wallet ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE wallet_transaction ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE warehouse ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE warehouse_code ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE warehouse_name ALTER tenant_id SET NOT NULL');
        $this->addSql('ALTER TABLE warehouse_parent ALTER tenant_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appeal_calculator ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE appeal_call ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE appeal_cooperation ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE appeal_postpone ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE appeal_question ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE appeal_schedule ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE appeal_status ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE appeal_tire_fitting ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE created_by ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry_order ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry_schedule ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry_deletion ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE car ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE car_recommendation ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE customer_transaction ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE wallet_transaction ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE organization ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE person ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE employee ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE expense ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE google_review_token ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE inventorization_part ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE income ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE inventorization ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE inventorization_close ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE employee_salary_end ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE income_part ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE motion ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE manufacturer ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE mc_work ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE part ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE mc_line ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE order_close ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE part_case ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE part_discount ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE part_price ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE mc_part ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE order_item ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE reservation ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE orders ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE order_payment ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE mc_equipment ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE part_cross ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE order_suspend ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE part_required_availability ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE part_supply ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE vehicle_model ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE publish ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE review ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE sms ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE sms_send ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE storage_part ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE wallet ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE warehouse ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE warehouse_code ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE warehouse_name ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE warehouse_parent ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE calendar_entry_order_info ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE note ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE note_delete ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE income_accrue ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE employee_salary ALTER tenant_id DROP NOT NULL');
        $this->addSql('ALTER TABLE user_permission ALTER tenant_id DROP NOT NULL');
    }
}
