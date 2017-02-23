<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170223215625 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE carmodel CHANGE carmake_id carmake_id INT DEFAULT NULL, CHANGE loaded loaded TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE carmodel ADD CONSTRAINT FK_41DA166E2EE4789A FOREIGN KEY (carmake_id) REFERENCES carmake (id)');
        $this->addSql('ALTER TABLE joblog CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL, CHANGE isprocessed isprocessed TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE carmodification CHANGE hp hp SMALLINT DEFAULT NULL, CHANGE doors doors INT DEFAULT NULL, CHANGE `from` `from` SMALLINT DEFAULT NULL, CHANGE till till SMALLINT DEFAULT NULL, CHANGE tank tank SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE carmodification ADD CONSTRAINT FK_DB5B685056B8B385 FOREIGN KEY (cargeneration_id) REFERENCES cargeneration (id)');
        $this->addSql('ALTER TABLE mileage CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE _order_id _order_id INT DEFAULT NULL, CHANGE car_id car_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_56BDF814A35F2858 ON mileage (_order_id)');
        $this->addSql('CREATE INDEX IDX_56BDF814C3C6F69F ON mileage (car_id)');
        $this->addSql('ALTER TABLE partcart CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL, CHANGE part_id part_id INT DEFAULT NULL, CHANGE _order_id _order_id INT DEFAULT NULL, CHANGE qty_stock qty_stock TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE _order DROP mileage_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE ownedsecurableitem_id ownedsecurableitem_id INT DEFAULT NULL, CHANGE car_id car_id INT DEFAULT NULL, CHANGE client_id client_id INT DEFAULT NULL, CHANGE checkpay checkpay TINYINT(1) DEFAULT NULL, CHANGE eid eid INT DEFAULT NULL, CHANGE paypoints paypoints TINYINT(1) DEFAULT NULL, CHANGE bonus bonus INT DEFAULT NULL, CHANGE points points TINYINT(1) DEFAULT NULL, CHANGE paycard paycard INT DEFAULT NULL');
        $this->addSql('ALTER TABLE activity_item CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE activity_id activity_id INT DEFAULT NULL, CHANGE item_id item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE messagesource CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE filecontent CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE meta_filemodel_id meta_filemodel_id INT DEFAULT NULL, CHANGE filemodel_id filemodel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE partitem CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE jobitem_id jobitem_id INT DEFAULT NULL, CHANGE part_id part_id INT DEFAULT NULL, CHANGE is_order is_order TINYINT(1) DEFAULT NULL, CHANGE qty qty NUMERIC(5, 1) NOT NULL, CHANGE _order_id _order_id INT DEFAULT NULL, CHANGE jobadvice_id jobadvice_id INT DEFAULT NULL, CHANGE motion_id motion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE partitem ADD CONSTRAINT FK_9054196FA35F2858 FOREIGN KEY (_order_id) REFERENCES _order (id)');
        $this->addSql('CREATE INDEX IDX_9054196F4CE34BEC ON partitem (part_id)');
        $this->addSql('ALTER TABLE keys_price CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE cnt cnt TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE jobitem CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE _user_id _user_id INT DEFAULT NULL, CHANGE _order_id _order_id INT DEFAULT NULL, CHANGE jobadvice_id jobadvice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE jobitem ADD CONSTRAINT FK_96A5AF75A35F2858 FOREIGN KEY (_order_id) REFERENCES _order (id)');
        $this->addSql('CREATE INDEX IDX_96A5AF755B1E621E ON jobitem (jobadvice_id)');
        $this->addSql('ALTER TABLE jobadvice CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL, CHANGE car_id car_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_EFE65F61C3C6F69F ON jobadvice (car_id)');
        $this->addSql('ALTER TABLE integrationpart_partcart CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE partcart_id partcart_id INT DEFAULT NULL, CHANGE integrationpart_id integrationpart_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE activity CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE ownedsecurableitem_id ownedsecurableitem_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE part CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL, CHANGE manufacturer_id manufacturer_id INT DEFAULT NULL, CHANGE negative negative TINYINT(1) DEFAULT NULL, CHANGE fractional fractional TINYINT(1) DEFAULT NULL, CHANGE reserved reserved INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_490F70C6A23B42D ON part (manufacturer_id)');
        $this->addSql('ALTER TABLE payment CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL, CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE _group CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE permitable_id permitable_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE motion CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL, CHANGE part_id part_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE item CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE modifiedbyuser__user_id modifiedbyuser__user_id INT DEFAULT NULL, CHANGE createdbyuser__user_id createdbyuser__user_id INT DEFAULT NULL, CHANGE _user_id _user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE permission CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE permitable_id permitable_id INT DEFAULT NULL, CHANGE permissions permissions TINYINT(1) DEFAULT NULL, CHANGE securableitem_id securableitem_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ownedsecurableitem CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE securableitem_id securableitem_id INT DEFAULT NULL, CHANGE owner__user_id owner__user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cache_price CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE term term TINYINT(1) DEFAULT NULL, CHANGE id_price id_price INT DEFAULT NULL, CHANGE id_d2m id_d2m INT DEFAULT NULL, CHANGE qty qty INT DEFAULT NULL, CHANGE prc_ok prc_ok TINYINT(1) DEFAULT NULL, CHANGE min_qty min_qty INT DEFAULT NULL, CHANGE type_cross type_cross TINYINT(1) DEFAULT NULL, CHANGE qid qid INT DEFAULT NULL, CHANGE grp_part grp_part TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE email CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE _user CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE person_id person_id INT DEFAULT NULL, CHANGE permitable_id permitable_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cargeneration ADD CONSTRAINT FK_99583F0A5E96AD46 FOREIGN KEY (carmodel_id) REFERENCES carmodel (id)');
        $this->addSql('ALTER TABLE pointtransaction CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL, CHANGE point_id point_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE jobinprocess CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE auditevent CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE modelid modelid INT DEFAULT NULL, CHANGE _user_id _user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE manufacturer CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL, CHANGE bitoriginal bitoriginal TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE person CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE ownedsecurableitem_id ownedsecurableitem_id INT DEFAULT NULL, CHANGE title_ownedcustomfield_id title_ownedcustomfield_id INT DEFAULT NULL, CHANGE primaryemail_email_id primaryemail_email_id INT DEFAULT NULL, CHANGE primaryaddress_address_id primaryaddress_address_id INT DEFAULT NULL, CHANGE title_customfield_id title_customfield_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE note CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE activity_id activity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE person_id person_id INT DEFAULT NULL, CHANGE wallet wallet INT NOT NULL, CHANGE eid eid INT DEFAULT NULL, CHANGE referal_client_id referal_client_id INT DEFAULT NULL, CHANGE point_id point_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('DROP INDEX IDX_GOSNOMER ON car');
        $this->addSql('ALTER TABLE car DROP mileage_id, DROP cargeneration_id, DROP make_carmake_id, DROP model_carmodel_id, DROP modification_carmodification_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL, CHANGE carmake_id carmake_id INT DEFAULT NULL, CHANGE carmodel_id carmodel_id INT DEFAULT NULL, CHANGE carmodification_id carmodification_id INT DEFAULT NULL, CHANGE year year INT DEFAULT NULL, CHANGE client_id client_id INT DEFAULT NULL, CHANGE eid eid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D2EE4789A FOREIGN KEY (carmake_id) REFERENCES carmake (id)');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D5E96AD46 FOREIGN KEY (carmodel_id) REFERENCES carmodel (id)');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_773DE69D126F525E ON car (item_id)');
        $this->addSql('CREATE INDEX IDX_773DE69D2EE4789A ON car (carmake_id)');
        $this->addSql('CREATE INDEX IDX_773DE69D5E96AD46 ON car (carmodel_id)');
        $this->addSql('CREATE INDEX IDX_773DE69D5B608288 ON car (carmodification_id)');
        $this->addSql('ALTER TABLE integrationpart CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL, CHANGE part_id part_id INT DEFAULT NULL, CHANGE gid gid INT DEFAULT NULL, CHANGE zc zc INT DEFAULT NULL, CHANGE qty qty SMALLINT DEFAULT NULL, CHANGE state state SMALLINT DEFAULT NULL, CHANGE committed committed TINYINT(1) DEFAULT NULL, CHANGE error error TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE _years CHANGE val val INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE carmake CHANGE loaded loaded TINYINT(1) DEFAULT NULL, CHANGE choose choose VARCHAR(255) DEFAULT NULL, CHANGE isParent isParent VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE securableitem CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE point CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL, CHANGE value value INT DEFAULT NULL, CHANGE person_item_id person_item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE currency CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE permitable CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT DEFAULT NULL, CHANGE _user_id _user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE filemodel CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE size size INT DEFAULT NULL, CHANGE item_id item_id INT DEFAULT NULL, CHANGE filecontent_id filecontent_id INT DEFAULT NULL, CHANGE relatedmodel_id relatedmodel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE _group__user CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE _group_id _group_id INT DEFAULT NULL, CHANGE _user_id _user_id INT DEFAULT NULL');
        $this->addSql('DROP TABLE diagrecord');

        $this->addSql('SET FOREIGN_KEY_CHECKS=0');
        $this->addSql('ALTER TABLE mileage ADD CONSTRAINT FK_56BDF814A35F2858 FOREIGN KEY (_order_id) REFERENCES _order (id)');
        $this->addSql('ALTER TABLE _years ENGINE=INNODB');
        $this->addSql('ALTER TABLE cargeneration ENGINE=INNODB');
        $this->addSql('ALTER TABLE carmodification ENGINE=INNODB');
        $this->addSql('ALTER TABLE mileage ADD CONSTRAINT FK_56BDF814C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE _order ADD CONSTRAINT FK_7F117F04C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE _order ADD CONSTRAINT FK_7F117F0419EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE partitem ADD CONSTRAINT FK_9054196F4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE jobitem ADD CONSTRAINT FK_96A5AF755B1E621E FOREIGN KEY (jobadvice_id) REFERENCES jobadvice (id)');
        $this->addSql('ALTER TABLE jobadvice ADD CONSTRAINT FK_EFE65F61C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE part ADD CONSTRAINT FK_490F70C6A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id)');
        $this->addSql('ALTER TABLE client DROP ratio');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D5B608288 FOREIGN KEY (carmodification_id) REFERENCES carmodification (id)');
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D126F525E');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D2EE4789A');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D19EB6921');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D5E96AD46');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455217BBB47');
        $this->addSql('ALTER TABLE jobitem DROP FOREIGN KEY FK_96A5AF75A35F2858');
        $this->addSql('ALTER TABLE carmodel DROP FOREIGN KEY FK_41DA166E2EE4789A');
        $this->addSql('ALTER TABLE cargeneration DROP FOREIGN KEY FK_99583F0A5E96AD46');
        $this->addSql('ALTER TABLE partitem DROP FOREIGN KEY FK_9054196FA35F2858');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D5B608288');
        $this->addSql('ALTER TABLE _order DROP FOREIGN KEY FK_7F117F04C3C6F69F');
        $this->addSql('ALTER TABLE _order DROP FOREIGN KEY FK_7F117F0419EB6921');
        $this->addSql('ALTER TABLE jobadvice DROP FOREIGN KEY FK_EFE65F61C3C6F69F');
        $this->addSql('ALTER TABLE jobitem DROP FOREIGN KEY FK_96A5AF755B1E621E');
        $this->addSql('ALTER TABLE mileage DROP FOREIGN KEY FK_56BDF814A35F2858');
        $this->addSql('ALTER TABLE mileage DROP FOREIGN KEY FK_56BDF814C3C6F69F');
        $this->addSql('ALTER TABLE part DROP FOREIGN KEY FK_490F70C6A23B42D');
        $this->addSql('ALTER TABLE partitem DROP FOREIGN KEY FK_9054196F4CE34BEC');
        $this->addSql('ALTER TABLE carmodification DROP FOREIGN KEY FK_DB5B685056B8B385');
        $this->addSql('DROP INDEX IDX_773DE69D126F525E ON car');
        $this->addSql('DROP INDEX IDX_773DE69D2EE4789A ON car');
        $this->addSql('DROP INDEX IDX_773DE69D5E96AD46 ON car');
        $this->addSql('DROP INDEX IDX_773DE69D5B608288 ON car');
        $this->addSql('DROP INDEX IDX_EFE65F61C3C6F69F ON jobadvice');
        $this->addSql('DROP INDEX IDX_96A5AF755B1E621E ON jobitem');
        $this->addSql('DROP INDEX IDX_490F70C6A23B42D ON part');
        $this->addSql('DROP INDEX IDX_9054196F4CE34BEC ON partitem');
        $this->addSql('DROP INDEX IDX_56BDF814A35F2858 ON mileage');
        $this->addSql('DROP INDEX IDX_56BDF814C3C6F69F ON mileage');

        $this->addSql('CREATE TABLE diagrecord (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT UNSIGNED DEFAULT NULL, _order_id INT UNSIGNED DEFAULT NULL, ammo_front TINYINT(1) DEFAULT NULL, ammo_rear TINYINT(1) DEFAULT NULL, sup_bear TINYINT(1) DEFAULT NULL, near_light TINYINT(1) DEFAULT NULL, far_light TINYINT(1) DEFAULT NULL, parking_light TINYINT(1) DEFAULT NULL, blinker TINYINT(1) DEFAULT NULL, plate_light TINYINT(1) DEFAULT NULL, stop_light TINYINT(1) DEFAULT NULL, horn TINYINT(1) DEFAULT NULL, dash_light TINYINT(1) DEFAULT NULL, warning_light TINYINT(1) DEFAULT NULL, handbrake TINYINT(1) DEFAULT NULL, air_filter TINYINT(1) DEFAULT NULL, driving_belt TINYINT(1) DEFAULT NULL, hv_wires TINYINT(1) DEFAULT NULL, eng_noise TINYINT(1) DEFAULT NULL, oil_level TINYINT(1) DEFAULT NULL, engine_mount TINYINT(1) DEFAULT NULL, antifreeze_level TINYINT(1) DEFAULT NULL, at_level TINYINT(1) DEFAULT NULL, clutch_level TINYINT(1) DEFAULT NULL, brake_level TINYINT(1) DEFAULT NULL, hydro_level TINYINT(1) DEFAULT NULL, shock_dust_front TINYINT(1) DEFAULT NULL, shock_dust_rear TINYINT(1) DEFAULT NULL, brake_disk_front TINYINT(1) DEFAULT NULL, brake_pad_front TINYINT(1) DEFAULT NULL, support_front TINYINT(1) DEFAULT NULL, bearing_front TINYINT(1) DEFAULT NULL, brake_disk_rear TINYINT(1) DEFAULT NULL, brake_pad_rear TINYINT(1) DEFAULT NULL, support_rear TINYINT(1) DEFAULT NULL, bearing_rear TINYINT(1) DEFAULT NULL, arm_lower_front TINYINT(1) DEFAULT NULL, arm_upper_front TINYINT(1) DEFAULT NULL, arm_lower_rear TINYINT(1) DEFAULT NULL, arm_upper_rear TINYINT(1) DEFAULT NULL, steer_tip TINYINT(1) DEFAULT NULL, steer_rod TINYINT(1) DEFAULT NULL, steer_rack TINYINT(1) DEFAULT NULL, engine_leak TINYINT(1) DEFAULT NULL, antifreeze_leak TINYINT(1) DEFAULT NULL, engine_mount_lower TINYINT(1) DEFAULT NULL, transmission_leak TINYINT(1) DEFAULT NULL, transmission_rubber TINYINT(1) DEFAULT NULL, driveshaft TINYINT(1) DEFAULT NULL, rear_reductor_leak TINYINT(1) DEFAULT NULL, front_reductor_leak TINYINT(1) DEFAULT NULL, transfer_leak TINYINT(1) DEFAULT NULL, front_stab TINYINT(1) DEFAULT NULL, front_arm_lower TINYINT(1) DEFAULT NULL, front_arm_upper TINYINT(1) DEFAULT NULL, rear_arm_lower TINYINT(1) DEFAULT NULL, rear_arm_upper TINYINT(1) DEFAULT NULL, rear_stab TINYINT(1) DEFAULT NULL, track_bar TINYINT(1) DEFAULT NULL, breaking_hose_front TINYINT(1) DEFAULT NULL, breaking_hose_rear TINYINT(1) DEFAULT NULL, steering_dust_boot TINYINT(1) DEFAULT NULL, hydro_leak TINYINT(1) DEFAULT NULL, spark INT DEFAULT NULL, engine_belt INT DEFAULT NULL, engine_oil INT DEFAULT NULL, gear_oil INT DEFAULT NULL, reductor_rear_oil INT DEFAULT NULL, reductor_front_oil INT DEFAULT NULL, transfer_oil INT DEFAULT NULL, hydro_oil INT DEFAULT NULL, filter_cabin INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE _group CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE permitable_id permitable_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE _group__user CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE _group_id _group_id INT UNSIGNED DEFAULT NULL, CHANGE _user_id _user_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE _order ADD mileage_id INT UNSIGNED DEFAULT NULL, CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE car_id car_id INT UNSIGNED DEFAULT NULL, CHANGE client_id client_id INT UNSIGNED DEFAULT NULL, CHANGE ownedsecurableitem_id ownedsecurableitem_id INT UNSIGNED DEFAULT NULL, CHANGE checkpay checkpay TINYINT(1) DEFAULT NULL, CHANGE eid eid INT UNSIGNED DEFAULT NULL, CHANGE paypoints paypoints TINYINT(1) DEFAULT NULL, CHANGE bonus bonus INT UNSIGNED DEFAULT NULL, CHANGE points points TINYINT(1) DEFAULT NULL, CHANGE paycard paycard INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE _user CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE person_id person_id INT UNSIGNED DEFAULT NULL, CHANGE permitable_id permitable_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE _years CHANGE val val INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE activity CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE ownedsecurableitem_id ownedsecurableitem_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE activity_item CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE activity_id activity_id INT UNSIGNED DEFAULT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE auditevent CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE modelid modelid INT UNSIGNED DEFAULT NULL, CHANGE _user_id _user_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE cache_price CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE term term TINYINT(1) DEFAULT NULL, CHANGE id_price id_price INT UNSIGNED DEFAULT NULL, CHANGE id_d2m id_d2m INT UNSIGNED DEFAULT NULL, CHANGE qty qty INT UNSIGNED DEFAULT NULL, CHANGE prc_ok prc_ok TINYINT(1) DEFAULT NULL, CHANGE min_qty min_qty INT UNSIGNED DEFAULT NULL, CHANGE type_cross type_cross TINYINT(1) DEFAULT NULL, CHANGE qid qid INT UNSIGNED DEFAULT NULL, CHANGE grp_part grp_part TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE car ADD mileage_id INT UNSIGNED DEFAULT NULL, ADD cargeneration_id INT DEFAULT NULL, ADD make_carmake_id TINYINT(1) DEFAULT NULL, ADD model_carmodel_id TINYINT(1) DEFAULT NULL, ADD modification_carmodification_id TINYINT(1) DEFAULT NULL, CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, CHANGE carmake_id carmake_id INT UNSIGNED DEFAULT NULL, CHANGE carmodel_id carmodel_id INT UNSIGNED DEFAULT NULL, CHANGE carmodification_id carmodification_id INT UNSIGNED DEFAULT NULL, CHANGE client_id client_id INT UNSIGNED DEFAULT NULL, CHANGE year year INT UNSIGNED DEFAULT NULL, CHANGE eid eid INT UNSIGNED DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_GOSNOMER ON car (gosnomer)');
        $this->addSql('ALTER TABLE carmake CHANGE loaded loaded TINYINT(1) DEFAULT \'0\', CHANGE choose choose VARCHAR(255) DEFAULT \'true\' COLLATE utf8_general_ci, CHANGE isParent isParent VARCHAR(255) DEFAULT \'true\' COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE carmodel CHANGE carmake_id carmake_id INT UNSIGNED DEFAULT NULL, CHANGE loaded loaded TINYINT(1) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE carmodification CHANGE hp hp SMALLINT UNSIGNED DEFAULT NULL, CHANGE doors doors TINYINT(1) DEFAULT NULL, CHANGE `from` `from` SMALLINT UNSIGNED DEFAULT NULL, CHANGE till till SMALLINT UNSIGNED DEFAULT NULL, CHANGE tank tank SMALLINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE client CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE person_id person_id INT UNSIGNED DEFAULT NULL, CHANGE wallet wallet INT DEFAULT 0 NOT NULL, CHANGE eid eid INT UNSIGNED DEFAULT NULL, CHANGE referal_client_id referal_client_id INT UNSIGNED DEFAULT NULL, CHANGE point_id point_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE currency CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE email CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE filecontent CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE meta_filemodel_id meta_filemodel_id INT UNSIGNED DEFAULT NULL, CHANGE filemodel_id filemodel_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE filemodel CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE size size INT UNSIGNED DEFAULT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, CHANGE filecontent_id filecontent_id INT UNSIGNED DEFAULT NULL, CHANGE relatedmodel_id relatedmodel_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE integrationpart CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, CHANGE part_id part_id INT UNSIGNED DEFAULT NULL, CHANGE gid gid INT UNSIGNED DEFAULT NULL, CHANGE zc zc INT UNSIGNED DEFAULT NULL, CHANGE qty qty SMALLINT UNSIGNED DEFAULT NULL, CHANGE state state SMALLINT UNSIGNED DEFAULT NULL, CHANGE committed committed TINYINT(1) DEFAULT NULL, CHANGE error error TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE integrationpart_partcart CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE partcart_id partcart_id INT UNSIGNED DEFAULT NULL, CHANGE integrationpart_id integrationpart_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE item CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE modifiedbyuser__user_id modifiedbyuser__user_id INT UNSIGNED DEFAULT NULL, CHANGE createdbyuser__user_id createdbyuser__user_id INT UNSIGNED DEFAULT NULL, CHANGE _user_id _user_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE jobadvice CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE car_id car_id INT UNSIGNED DEFAULT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE jobinprocess CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE jobitem CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE _order_id _order_id INT UNSIGNED DEFAULT NULL, CHANGE jobadvice_id jobadvice_id INT UNSIGNED DEFAULT NULL, CHANGE _user_id _user_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE joblog CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, CHANGE isprocessed isprocessed TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE keys_price CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE cnt cnt TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE manufacturer CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, CHANGE bitoriginal bitoriginal TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE messagesource CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE mileage CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE _order_id _order_id INT UNSIGNED DEFAULT NULL, CHANGE car_id car_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE motion CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, CHANGE part_id part_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE note CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE activity_id activity_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE ownedsecurableitem CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE securableitem_id securableitem_id INT UNSIGNED DEFAULT NULL, CHANGE owner__user_id owner__user_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE part CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE manufacturer_id manufacturer_id INT UNSIGNED DEFAULT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, CHANGE negative negative TINYINT(1) DEFAULT NULL, CHANGE fractional fractional TINYINT(1) DEFAULT NULL, CHANGE reserved reserved INT UNSIGNED DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE partcart CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, CHANGE part_id part_id INT UNSIGNED DEFAULT NULL, CHANGE _order_id _order_id INT UNSIGNED DEFAULT NULL, CHANGE qty_stock qty_stock TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE partitem CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE part_id part_id INT UNSIGNED DEFAULT NULL, CHANGE _order_id _order_id INT UNSIGNED DEFAULT NULL, CHANGE jobitem_id jobitem_id INT UNSIGNED DEFAULT NULL, CHANGE is_order is_order TINYINT(1) DEFAULT NULL, CHANGE qty qty NUMERIC(5, 1) DEFAULT \'0.0\' NOT NULL, CHANGE jobadvice_id jobadvice_id INT UNSIGNED DEFAULT NULL, CHANGE motion_id motion_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE payment CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, CHANGE client_id client_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE permission CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE permitable_id permitable_id INT UNSIGNED DEFAULT NULL, CHANGE permissions permissions TINYINT(1) DEFAULT NULL, CHANGE securableitem_id securableitem_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE permitable CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, CHANGE _user_id _user_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE person CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE ownedsecurableitem_id ownedsecurableitem_id INT UNSIGNED DEFAULT NULL, CHANGE title_ownedcustomfield_id title_ownedcustomfield_id INT UNSIGNED DEFAULT NULL, CHANGE primaryemail_email_id primaryemail_email_id INT UNSIGNED DEFAULT NULL, CHANGE primaryaddress_address_id primaryaddress_address_id INT UNSIGNED DEFAULT NULL, CHANGE title_customfield_id title_customfield_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE point CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, CHANGE value value INT UNSIGNED DEFAULT NULL, CHANGE person_item_id person_item_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE pointtransaction CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL, CHANGE point_id point_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE securableitem CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE item_id item_id INT UNSIGNED DEFAULT NULL');

        $this->addSql('ALTER TABLE client ADD ratio TINYINT(1) DEFAULT NULL');
    }
}
