<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Balance\Entity\BalanceView;
use App\Costil;
use App\CreatedBy\Entity\CreatedByView;
use App\Employee\Entity\SalaryView;
use App\Note\Entity\NoteView;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;

final class Version20200626085701 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP VIEW IF EXISTS balance_view');
        $this->addSql('DROP VIEW IF EXISTS part_view');
        $this->addSql('DROP VIEW IF EXISTS salary_view');
        $this->addSql('DROP VIEW IF EXISTS created_by_view');
        $this->addSql('DROP VIEW IF EXISTS note_view');

        $this->addSql('DROP SEQUENCE car_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE car_recommendation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE car_recommendation_part_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE employee_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE expense_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE income_part_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mc_equipment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mc_line_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mc_part_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mc_work_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE operand_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE order_item_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE orders_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE vehicle_model_id_seq CASCADE');

        // ---

        $this->addSql('ALTER TABLE car_recommendation_part ADD recommendation_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE car_recommendation_part
            SET recommendation_uuid = sub.uuid
            FROM (SELECT id, uuid FROM car_recommendation) sub
            WHERE car_recommendation_part.recommendation_id = sub.id
        ');
        $this->addSql('ALTER TABLE car_recommendation_part DROP CONSTRAINT fk_ddc72d65d173940b');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER recommendation_id DROP DEFAULT');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER recommendation_id TYPE UUID USING (recommendation_uuid)');
        $this->addSql('ALTER TABLE car_recommendation_part DROP recommendation_uuid');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.recommendation_id IS \'(DC2Type:recommendation_id)\'');

        // ---

        $this->addSql('DROP INDEX uniq_ddc72d65d17f50a6');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE car_recommendation_part DROP uuid');
        $this->addSql('COMMENT ON COLUMN car_recommendation_part.id IS \'(DC2Type:recommendation_part_id)\'');

        // ---

        $this->addSql('ALTER TABLE car_recommendation ADD realization_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE car_recommendation
            SET realization_uuid = sub.uuid
            FROM (SELECT id, uuid FROM order_item) sub
            WHERE car_recommendation.realization = sub.id
        ');
        $this->addSql('ALTER TABLE car_recommendation ALTER realization DROP DEFAULT');
        $this->addSql('ALTER TABLE car_recommendation ALTER realization TYPE UUID USING (realization_uuid)');
        $this->addSql('ALTER TABLE car_recommendation DROP realization_uuid');
        $this->addSql('COMMENT ON COLUMN car_recommendation.realization IS \'(DC2Type:uuid)\'');

        // ---

        $this->addSql('ALTER TABLE car_recommendation ADD car_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE car_recommendation
            SET car_uuid = sub.uuid
            FROM (SELECT id, uuid FROM car) sub
            WHERE car_recommendation.car_id = sub.id
        ');
        $this->addSql('ALTER TABLE car_recommendation DROP CONSTRAINT fk_8e4baaf2c3c6f69f');
        $this->addSql('ALTER TABLE car_recommendation ALTER car_id DROP DEFAULT');
        $this->addSql('ALTER TABLE car_recommendation ALTER car_id TYPE UUID USING (car_uuid)');
        $this->addSql('ALTER TABLE car_recommendation DROP car_uuid');
        $this->addSql('COMMENT ON COLUMN car_recommendation.id IS \'(DC2Type:recommendation_id)\'');

        // ---

        $this->addSql('DROP INDEX uniq_8e4baaf2d17f50a6');
        $this->addSql('ALTER TABLE car_recommendation ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE car_recommendation ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE car_recommendation DROP uuid');

        // ---

        $this->addSql('DROP INDEX uniq_773de69dd17f50a6');
        $this->addSql('ALTER TABLE car ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE car ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE car DROP uuid');
        $this->addSql('COMMENT ON COLUMN car.id IS \'(DC2Type:car_id)\'');
        $this->addSql('COMMENT ON COLUMN car_recommendation.car_id IS \'(DC2Type:car_id)\'');

        // ---

        $this->addSql('ALTER TABLE organization ADD uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE organization
            SET uuid = sub.uuid
            FROM (SELECT id, uuid FROM operand) sub
            WHERE organization.id = sub.id
        ');
        $this->addSql('ALTER TABLE organization DROP CONSTRAINT fk_c1ee637cbf396750');
        $this->addSql('ALTER TABLE organization ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE organization ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE organization DROP uuid');
        $this->addSql('COMMENT ON COLUMN organization.id IS \'(DC2Type:operand_id)\'');

        $this->addSql('ALTER TABLE person ADD uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE person
            SET uuid = sub.uuid
            FROM (SELECT id, uuid FROM operand) sub
            WHERE person.id = sub.id
        ');
        $this->addSql('ALTER TABLE person DROP CONSTRAINT fk_34dcd176bf396750');
        $this->addSql('ALTER TABLE person ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE person ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE person DROP uuid');
        $this->addSql('COMMENT ON COLUMN person.id IS \'(DC2Type:operand_id)\'');

        $this->addSql('DROP INDEX uniq_83e03ce6d17f50a6');
        $this->addSql('ALTER TABLE operand ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE operand ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE operand DROP uuid');
        $this->addSql('COMMENT ON COLUMN operand.id IS \'(DC2Type:operand_id)\'');

        // ---

        $this->addSql('ALTER TABLE expense ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE expense ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE expense DROP uuid');
        $this->addSql('COMMENT ON COLUMN expense.id IS \'(DC2Type:uuid)\'');

        // ---

        $this->addSql('ALTER TABLE income ADD accrued_by_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE income
            SET accrued_by_uuid = sub.uuid
            FROM (SELECT id, uuid FROM users) sub
            WHERE income.accrued_by_id = sub.id
        ');
        $this->addSql('ALTER TABLE income DROP CONSTRAINT fk_3fa862d0748c73b5');
        $this->addSql('ALTER TABLE income ALTER accrued_by_id DROP DEFAULT');
        $this->addSql('ALTER TABLE income ALTER accrued_by_id TYPE UUID USING (accrued_by_uuid)');
        $this->addSql('ALTER TABLE income DROP accrued_by_uuid');
        $this->addSql('COMMENT ON COLUMN income.accrued_by_id IS \'(DC2Type:user_id)\'');

        // ---

        $this->addSql('ALTER TABLE income_part ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE income_part ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE income_part DROP uuid');
        $this->addSql('COMMENT ON COLUMN income_part.id IS \'(DC2Type:income_part_id)\'');

        // ---

        $this->addSql('ALTER TABLE mc_part ADD line_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE mc_part
            SET line_uuid = sub.uuid
            FROM (SELECT id, uuid FROM mc_line) sub
            WHERE mc_part.line_id = sub.id
        ');
        $this->addSql('ALTER TABLE mc_part ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE mc_part ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE mc_part DROP CONSTRAINT fk_2b65786f4d7b7542');
        $this->addSql('ALTER TABLE mc_part ALTER line_id DROP DEFAULT');
        $this->addSql('ALTER TABLE mc_part ALTER line_id TYPE UUID USING (line_uuid)');
        $this->addSql('ALTER TABLE mc_part DROP uuid');
        $this->addSql('ALTER TABLE mc_part DROP line_uuid');
        $this->addSql('COMMENT ON COLUMN mc_part.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN mc_part.line_id IS \'(DC2Type:uuid)\'');

        // ---

        $this->addSql('ALTER TABLE mc_line ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE mc_line ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE mc_line DROP uuid');
        $this->addSql('COMMENT ON COLUMN mc_line.id IS \'(DC2Type:uuid)\'');

        // ---

        $this->addSql('ALTER TABLE mc_line ADD equipment_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE mc_line
            SET equipment_uuid = sub.uuid
            FROM (SELECT id, uuid FROM mc_equipment) sub
            WHERE mc_line.equipment_id = sub.id
        ');
        $this->addSql('ALTER TABLE mc_line DROP CONSTRAINT fk_b37ebc5f517fe9fe');
        $this->addSql('ALTER TABLE mc_line ALTER equipment_id DROP DEFAULT');
        $this->addSql('ALTER TABLE mc_line ALTER equipment_id TYPE UUID USING (equipment_uuid)');
        $this->addSql('ALTER TABLE mc_line DROP equipment_uuid');
        $this->addSql('COMMENT ON COLUMN mc_line.equipment_id IS \'(DC2Type:mc_equipment_id)\'');

        // ---

        $this->addSql('ALTER TABLE mc_line ADD work_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE mc_line
            SET work_uuid = sub.uuid
            FROM (SELECT id, uuid FROM mc_work) sub
            WHERE mc_line.work_id = sub.id
        ');
        $this->addSql('ALTER TABLE mc_line DROP CONSTRAINT fk_b37ebc5fbb3453db');
        $this->addSql('ALTER TABLE mc_line ALTER work_id DROP DEFAULT');
        $this->addSql('ALTER TABLE mc_line ALTER work_id TYPE UUID USING (work_uuid)');
        $this->addSql('ALTER TABLE mc_line DROP work_uuid');
        $this->addSql('COMMENT ON COLUMN mc_line.work_id IS \'(DC2Type:uuid)\'');

        // ---

        $this->addSql('ALTER TABLE mc_work ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE mc_work ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE mc_work DROP uuid');
        $this->addSql('COMMENT ON COLUMN mc_work.id IS \'(DC2Type:uuid)\'');

        // ---

        $this->addSql('ALTER TABLE mc_equipment ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE mc_equipment ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE mc_equipment DROP uuid');
        $this->addSql('COMMENT ON COLUMN mc_equipment.id IS \'(DC2Type:mc_equipment_id)\'');

        // ---

        $this->addSql('ALTER TABLE reservation ADD order_item_part_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE reservation
            SET order_item_part_uuid = sub.uuid
            FROM (SELECT id, uuid FROM order_item) sub
            WHERE reservation.order_item_part_id = sub.id
        ');
        $this->addSql('ALTER TABLE reservation ALTER order_item_part_id DROP DEFAULT');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT fk_42c84955437ef9d2');
        $this->addSql('ALTER TABLE reservation ALTER order_item_part_id TYPE UUID USING (order_item_part_uuid)');
        $this->addSql('ALTER TABLE reservation DROP order_item_part_uuid');
        $this->addSql('COMMENT ON COLUMN reservation.order_item_part_id IS \'(DC2Type:uuid)\'');

        // ---

        $this->addSql('ALTER TABLE order_item_group ADD uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE order_item_group
            SET uuid = sub.uuid
            FROM (SELECT id, uuid FROM order_item) sub
            WHERE order_item_group.id = sub.id
        ');
        $this->addSql('ALTER TABLE order_item_group DROP CONSTRAINT fk_f4bda240bf396750');
        $this->addSql('ALTER TABLE order_item_group ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item_group ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE order_item_group DROP uuid');
        $this->addSql('COMMENT ON COLUMN order_item_group.id IS \'(DC2Type:uuid)\'');

        // ---

        $this->addSql('ALTER TABLE order_item_part ADD uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE order_item_part
            SET uuid = sub.uuid
            FROM (SELECT id, uuid FROM order_item) sub
            WHERE order_item_part.id = sub.id
        ');
        $this->addSql('ALTER TABLE order_item_part DROP CONSTRAINT fk_3db84fc5bf396750');
        $this->addSql('ALTER TABLE order_item_part ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item_part ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE order_item_part DROP uuid');
        $this->addSql('COMMENT ON COLUMN order_item_part.id IS \'(DC2Type:uuid)\'');

        // ---

        $this->addSql('ALTER TABLE order_item_service ADD uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE order_item_service
            SET uuid = sub.uuid
            FROM (SELECT id, uuid FROM order_item) sub
            WHERE order_item_service.id = sub.id
        ');
        $this->addSql('ALTER TABLE order_item_service DROP CONSTRAINT fk_ee0028ecbf396750');
        $this->addSql('ALTER TABLE order_item_service ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item_service ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE order_item_service DROP uuid');
        $this->addSql('COMMENT ON COLUMN order_item_service.id IS \'(DC2Type:uuid)\'');

        // ---

        $this->addSql('ALTER TABLE order_item ADD order_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE order_item
            SET order_uuid = sub.uuid
            FROM (SELECT id, uuid FROM orders) sub
            WHERE order_item.order_id = sub.id
        ');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT fk_52ea1f098d9f6d38');
        $this->addSql('ALTER TABLE order_item ALTER order_id DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item ALTER order_id TYPE UUID USING (order_uuid)');
        $this->addSql('ALTER TABLE order_item DROP order_uuid');
        $this->addSql('COMMENT ON COLUMN order_item.order_id IS \'(DC2Type:order_id)\'');

        // ---

        $this->addSql('ALTER TABLE order_item ADD parent_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE order_item
            SET parent_uuid = sub.uuid
            FROM (SELECT id, uuid FROM order_item) sub
            WHERE order_item.parent_id = sub.id
        ');
        $this->addSql('ALTER TABLE order_item DROP CONSTRAINT fk_52ea1f09727aca70');
        $this->addSql('ALTER TABLE order_item ALTER parent_id DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item ALTER parent_id TYPE UUID USING (parent_uuid)');
        $this->addSql('ALTER TABLE order_item DROP parent_uuid');
        $this->addSql('COMMENT ON COLUMN order_item.parent_id IS \'(DC2Type:uuid)\'');

        // ---

        $this->addSql('ALTER TABLE order_item ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE order_item DROP uuid');
        $this->addSql('COMMENT ON COLUMN order_item.id IS \'(DC2Type:uuid)\'');

        // ---

        $this->addSql('ALTER TABLE order_payment ADD order_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE order_payment
            SET order_uuid = sub.uuid
            FROM (SELECT id, uuid FROM orders) sub
            WHERE order_payment.order_id = sub.id
        ');
        $this->addSql('ALTER TABLE order_payment DROP CONSTRAINT fk_9b522d468d9f6d38');
        $this->addSql('ALTER TABLE order_payment ALTER order_id DROP DEFAULT');
        $this->addSql('ALTER TABLE order_payment ALTER order_id TYPE UUID USING (order_uuid)');
        $this->addSql('ALTER TABLE order_payment DROP order_uuid');
        $this->addSql('COMMENT ON COLUMN order_payment.order_id IS \'(DC2Type:order_id)\'');

        // ---

        $this->addSql('ALTER TABLE order_suspend DROP CONSTRAINT fk_c789f0d18d9f6d38');
        $this->addSql('ALTER TABLE order_suspend ALTER order_id DROP DEFAULT');
        $this->addSql('ALTER TABLE order_suspend ALTER order_id TYPE UUID USING (order_uuid)');
        $this->addSql('ALTER TABLE order_suspend DROP order_uuid');
        $this->addSql('COMMENT ON COLUMN order_suspend.order_id IS \'(DC2Type:order_id)\'');

        // ---

        $this->addSql('ALTER TABLE users_password ADD user_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE users_password
            SET user_uuid = sub.uuid
            FROM (SELECT id, uuid FROM users) sub
            WHERE users_password.user_id = sub.id
        ');
        $this->addSql('ALTER TABLE users_password DROP CONSTRAINT fk_d54fa2d5a76ed395');
        $this->addSql('ALTER TABLE users_password ALTER user_id DROP DEFAULT');
        $this->addSql('ALTER TABLE users_password ALTER user_id TYPE UUID USING (user_uuid)');
        $this->addSql('ALTER TABLE users_password DROP user_uuid');
        $this->addSql('COMMENT ON COLUMN users_password.user_id IS \'(DC2Type:user_id)\'');

        // ---

        $this->addSql('DROP INDEX uniq_b53af235d17f50a6');
        $this->addSql('ALTER TABLE vehicle_model ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE vehicle_model ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE vehicle_model DROP uuid');
        $this->addSql('COMMENT ON COLUMN vehicle_model.id IS \'(DC2Type:vehicle_id)\'');

        // ---

        $this->addSql('ALTER TABLE orders ADD number VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE orders SET number = id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE96901F54 ON orders (number)');
        $this->addSql('ALTER TABLE orders ALTER number SET NOT NULL');
        $this->addSql('CREATE SEQUENCE order_number');
        $this->addSql('SELECT setval(\'order_number\', (SELECT MAX(id) FROM orders))');

        // ---

        $this->addSql('ALTER TABLE orders ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE orders ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE orders DROP uuid');
        $this->addSql('COMMENT ON COLUMN orders.id IS \'(DC2Type:order_id)\'');

        // ---

        $this->addSql('ALTER TABLE orders ADD closed_by_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE orders
            SET closed_by_uuid = sub.uuid
            FROM (SELECT id, uuid FROM users) sub
            WHERE orders.closed_by_id = sub.id
        ');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT fk_e52ffdeee1fa7797');
        $this->addSql('ALTER TABLE orders ALTER closed_by_id DROP DEFAULT');
        $this->addSql('ALTER TABLE orders ALTER closed_by_id TYPE UUID USING (closed_by_uuid)');
        $this->addSql('ALTER TABLE orders DROP closed_by_uuid');
        $this->addSql('COMMENT ON COLUMN orders.closed_by_id IS \'(DC2Type:user_id)\'');

        // ---

        $this->addSql('ALTER TABLE orders ADD worker_uuid UUID DEFAULT NULL');
        $this->addSql('
            UPDATE orders
            SET worker_uuid = sub.uuid
            FROM (SELECT id, uuid FROM employee) sub
            WHERE orders.worker_id = sub.id
        ');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT fk_e52ffdee6b20ba36');
        $this->addSql('ALTER TABLE orders ALTER worker_id DROP DEFAULT');
        $this->addSql('ALTER TABLE orders ALTER worker_id TYPE UUID USING (worker_uuid)');
        $this->addSql('ALTER TABLE orders DROP worker_uuid');
        $this->addSql('COMMENT ON COLUMN orders.worker_id IS \'(DC2Type:employee_id)\'');

        // ---

        $this->addSql('ALTER TABLE employee ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE employee ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE employee DROP uuid');
        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at)
            SELECT e.id, \''.Costil::OLD_USER.'\'::uuid, e.hired_at
            FROM employee e
        ');
        $this->addSql('COMMENT ON COLUMN employee.id IS \'(DC2Type:employee_id)\'');

        // ---

        $this->addSql('DROP INDEX uniq_1483a5e9d17f50a6');
        $this->addSql('ALTER TABLE users ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE users ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE users DROP uuid');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:user_id)\'');

        // ---

        $this->addSql('ALTER TABLE car_recommendation_part ADD CONSTRAINT FK_DDC72D65D173940B FOREIGN KEY (recommendation_id) REFERENCES car_recommendation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mc_part ADD CONSTRAINT FK_2B65786F4D7B7542 FOREIGN KEY (line_id) REFERENCES mc_line (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mc_line ADD CONSTRAINT FK_B37EBC5FBB3453DB FOREIGN KEY (work_id) REFERENCES mc_work (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_8E4BAAF2C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_suspend ADD CONSTRAINT FK_C789F0D18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item_part ADD CONSTRAINT FK_3DB84FC5BF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mc_line ADD CONSTRAINT FK_B37EBC5F517FE9FE FOREIGN KEY (equipment_id) REFERENCES mc_equipment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE income ADD CONSTRAINT FK_3FA862D0748C73B5 FOREIGN KEY (accrued_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637CBF396750 FOREIGN KEY (id) REFERENCES operand (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176BF396750 FOREIGN KEY (id) REFERENCES operand (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09727ACA70 FOREIGN KEY (parent_id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item_group ADD CONSTRAINT FK_F4BDA240BF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEE1FA7797 FOREIGN KEY (closed_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE6B20BA36 FOREIGN KEY (worker_id) REFERENCES employee (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item_service ADD CONSTRAINT FK_EE0028ECBF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955437EF9D2 FOREIGN KEY (order_item_part_id) REFERENCES order_item_part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_payment ADD CONSTRAINT FK_9B522D468D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_password ADD CONSTRAINT FK_4E836D0FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql(BalanceView::sql());
        $this->addSql(SalaryView::sql());
        $this->addSql(CreatedByView::sql());
        $this->addSql(NoteView::sql());
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        throw new LogicException('Huy pizda skovoroda.');
    }
}
