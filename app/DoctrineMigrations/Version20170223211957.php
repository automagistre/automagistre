<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170223211957 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX search ON note (activity_id)');
        $this->addSql('CREATE INDEX search ON securableitem (item_id)');
        $this->addSql('CREATE INDEX search ON ownedsecurableitem (securableitem_id)');
        $this->addSql('CREATE INDEX search ON _order (ownedsecurableitem_id)');
        $this->addSql('CREATE INDEX search ON filemodel (filecontent_id)');
        $this->addSql('CREATE INDEX search2 ON filemodel (relatedmodel_id)');
        $this->addSql('SELECT SLEEP(5)');

        /* BEGIN link `note` to `_order` */
        $this->addSql('ALTER TABLE note ADD order_id INT(11) UNSIGNED DEFAULT NULL');

        $this->addSql('
            UPDATE note
              JOIN activity ON activity.id = note.activity_id
              JOIN activity_item ON activity_item.activity_id = activity.id
              JOIN securableitem ON securableitem.item_id = activity_item.item_id
              JOIN ownedsecurableitem ON ownedsecurableitem.securableitem_id = securableitem.id
              JOIN _order ON _order.ownedsecurableitem_id = ownedsecurableitem.id
            SET note.order_id = _order.id
        ');

        $this->addSql('DELETE FROM note WHERE order_id IS NULL');
        $this->addSql('ALTER TABLE note MODIFY order_id INT(11) UNSIGNED NOT NULL ');

        /* BEGIN link orders report with orders */
        $this->addSql('ALTER TABLE filecontent ADD order_id INT(11) UNSIGNED DEFAULT NULL');

        $this->addSql('
            UPDATE filecontent
              JOIN filemodel ON filemodel.filecontent_id = filecontent.id
              JOIN note ON note.id = filemodel.relatedmodel_id
            SET filecontent.order_id = note.order_id
        ');

        $this->addSql('
            UPDATE filecontent
              JOIN filemodel ON filemodel.filecontent_id = filecontent.id
              JOIN `_order` ON substr(filemodel.name, 10, 4) = `_order`.id
            SET filecontent.order_id = `_order`.id
            WHERE filecontent.order_id IS NULL
        ');

        $this->addSql('ALTER TABLE filecontent MODIFY order_id INT(11) UNSIGNED NOT NULL ');

        /* Try to set car_id to mileage where mileage is null */
        $this->addSql('
            UPDATE mileage
              JOIN (
                     SELECT
                       o.id,
                       MAX(o2.car_id) AS car_id
                     FROM _order o
                       JOIN _order o2 ON o2.client_id = o.client_id AND o2.car_id IS NOT NULL
                      GROUP BY o.id
                   ) AS o3 ON o3.id = mileage._order_id
            SET mileage.car_id = o3.car_id
            WHERE mileage.car_id IS NULL        
        ');
        /* Remove mileage which not linked to car */
        $this->addSql('DELETE FROM mileage WHERE mileage.car_id IS NULL');

        /* Remove mileage which linked to deleted car */
        $this->addSql('
            DELETE mileage FROM mileage
            LEFT JOIN car ON mileage.car_id = car.id
            WHERE car.id IS NULL
        ');

        /* Move email to Person */
        $this->addSql('ALTER TABLE person ADD email VARCHAR(255) DEFAULT NULL');
        $this->addSql('
            UPDATE person
                LEFT JOIN email ON email.id = person.primaryemail_email_id
            SET person.email = email.emailaddress
        ');

        /* Set orders.car_id to null where order linked to deleted car */
        $this->addSql('
            UPDATE _order
              LEFT JOIN car ON car.id = _order.car_id
            SET _order.car_id = NULL
            WHERE _order.car_id IS NOT NULL AND car.id IS NULL
        ');

        /* Set orders.client_id to null where order linked to deleted client */
        $this->addSql('
            UPDATE _order
              LEFT JOIN client ON client.id = _order.client_id
            SET _order.client_id = NULL
            WHERE _order.client_id IS NOT NULL AND client.id IS NULL
        ');

        /* Delete payment which linked to deleted client */
        $this->addSql('
            DELETE payment FROM payment
            LEFT JOIN client ON client.id = payment.client_id
            WHERE payment.client_id IS NOT NULL AND client.id IS NULL
        ');

        /* Delete job_advice which linked to deleted car */
        $this->addSql('
            DELETE jobadvice FROM jobadvice
            LEFT JOIN car ON car.id = jobadvice.car_id
            WHERE jobadvice.car_id IS NOT NULL AND car.id IS NULL
        ');

        /* Set jobitem._user_id to NULL which linked to deleted user */
        $this->addSql('
            UPDATE jobitem
            LEFT JOIN _user ON _user.id = jobitem._user_id
            SET jobitem._user_id = NULL
            WHERE jobitem._user_id IS NOT NULL AND _user.id IS NULL
        ');

        /** Delete PartItem which linked to deleted Parts */
        $this->addSql('
            DELETE partitem FROM partitem
              LEFT JOIN part ON part.id = partitem.part_id
            WHERE partitem.part_id IS NOT NULL AND part.id IS NULL
        ');

        /* Delete motion which linked to deleted part_item */
        $this->addSql('
            DELETE motion FROM motion
            LEFT JOIN partitem ON partitem.id = motion.part_id
            WHERE motion.part_id IS NOT NULL AND partitem.id IS NULL
        ');

        $this->addSql('DROP INDEX search ON note');
        $this->addSql('DROP INDEX search ON securableitem');
        $this->addSql('DROP INDEX search ON ownedsecurableitem');
        $this->addSql('DROP INDEX search ON _order');
        $this->addSql('DROP INDEX search ON filemodel');
        $this->addSql('DROP INDEX search2 ON filemodel');

        $this->addSql('DROP TABLE _group');
        $this->addSql('DROP TABLE _group__user');
        $this->addSql('DROP TABLE _order_read');
        $this->addSql('DROP TABLE _years');
        $this->addSql('DROP TABLE activelanguage');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_item');
        $this->addSql('DROP TABLE actual_permissions_cache');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE auditevent');
        $this->addSql('DROP TABLE basecustomfield');
        $this->addSql('DROP TABLE calculatedderivedattributemetadata');
        $this->addSql('DROP TABLE client_read');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE customfield');
        $this->addSql('DROP TABLE dashboard');
        $this->addSql('DROP TABLE derivedattributemetadata');
        $this->addSql('DROP TABLE diagrecord');
        $this->addSql('DROP TABLE dropdowndependencyderivedattributemetadata');
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP TABLE emailaccount');
        $this->addSql('DROP TABLE emailbox');
        $this->addSql('DROP TABLE emailfolder');
        $this->addSql('DROP TABLE emailmessage');
        $this->addSql('DROP TABLE emailmessage_read');
        $this->addSql('DROP TABLE emailmessagecontent');
        $this->addSql('DROP TABLE emailmessagerecipient');
        $this->addSql('DROP TABLE emailmessagesender');
        $this->addSql('DROP TABLE emailmessagesenderror');
        $this->addSql('DROP TABLE emailsignature');
        $this->addSql('DROP TABLE filemodel');
        $this->addSql('DROP TABLE globalmetadata');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE jobinprocess');
        $this->addSql('DROP TABLE joblog');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE messagesource');
        $this->addSql('DROP TABLE messagetranslation');
        $this->addSql('DROP TABLE note_read');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notificationmessage');
        $this->addSql('DROP TABLE ownedcustomfield');
        $this->addSql('DROP TABLE ownedsecurableitem');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE permitable');
        $this->addSql('DROP TABLE perusermetadata');
        $this->addSql('DROP TABLE point');
        $this->addSql('DROP TABLE pointtransaction');
        $this->addSql('DROP TABLE portlet');
        $this->addSql('DROP TABLE savedsearch');
        $this->addSql('DROP TABLE securableitem');
        $this->addSql('DROP TABLE integrationpart');
        $this->addSql('DROP TABLE integrationpart_partcart');
        $this->addSql('DROP TABLE partcart');

        $this->addSql('DROP INDEX EID_IDX ON car');
        $this->addSql('DROP INDEX IDX_GOSNOMER ON car');
        $this->addSql('DROP INDEX sprite_id ON car');
        $this->addSql('DROP INDEX idx_car_client ON car');
        $this->addSql('DROP INDEX uq_vin ON car');
        $this->addSql('DROP INDEX UQ_3a7293440d99b39c56ff99074677931de71144cb ON car');
        $this->addSql('DROP INDEX unique_make ON carmake');
        $this->addSql('DROP INDEX IDX_MAKES_FOLDER ON carmake');
        $this->addSql('DROP INDEX IDX_MODEL_FOLDER ON carmodel');
        $this->addSql('DROP INDEX fk_am_models_am_makes ON carmodel');
        $this->addSql('DROP INDEX IDX_MODIF_FOLDER ON carmodification');
        $this->addSql('DROP INDEX idx_modif_parent ON carmodification');
        $this->addSql('DROP INDEX idx_order_car ON _order');
        $this->addSql('DROP INDEX idx_order_client ON _order');
        $this->addSql('DROP INDEX _order_id ON partitem');
        $this->addSql('DROP INDEX _order_id ON jobitem');
        $this->addSql('DROP INDEX part_uniq ON part');
        $this->addSql('DROP INDEX uq_2f9f20ae60de87f7bdd974b52941c30e287c6eef ON _user');
        $this->addSql('DROP INDEX IDX_GENERAT_FOLDER ON cargeneration');
        $this->addSql('DROP INDEX idx_generat_parent ON cargeneration');
        $this->addSql('DROP INDEX EID_IDX ON client');
        $this->addSql('DROP INDEX IDX_CLIENT_PERSON ON client');
        $this->addSql('DROP INDEX uq_c17cf66b38ac3bd928a6ebf320320881ce022754 ON manufacturer');
        $this->addSql('DROP INDEX uq_e866d1f7bc3130384a2fd1ad1ddd50921a0101b9 ON manufacturer');
        $this->addSql('DROP INDEX idx_person_lastname ON person');
        $this->addSql('DROP INDEX idx_person_firstname ON person');
        $this->addSql('DROP INDEX idx_person_phone ON person');

        $this->addSql('ALTER TABLE car ADD creted_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL , DROP item_id, DROP mileage_id, DROP eid, DROP cargeneration_id, DROP make_carmake_id, DROP model_carmodel_id, DROP modification_carmodification_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE carmake_id car_manufacturer_id INT DEFAULT NULL, CHANGE carmodel_id car_model_id INT DEFAULT NULL, CHANGE carmodification_id car_modification_id INT DEFAULT NULL, CHANGE year year INT DEFAULT NULL, CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE carmake DROP folder, DROP link, DROP loaded, DROP choose, DROP isParent, CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE carmodel DROP folder, DROP link, DROP loaded, CHANGE carmake_id carmake_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE carmodification DROP folder, DROP link, CHANGE hp hp SMALLINT DEFAULT NULL, CHANGE doors doors SMALLINT DEFAULT NULL, CHANGE `from` `from` SMALLINT DEFAULT NULL, CHANGE till till SMALLINT DEFAULT NULL, CHANGE tank tank SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE cargeneration DROP folder');
        $this->addSql('ALTER TABLE mileage CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE _order_id order_id INT DEFAULT NULL, CHANGE car_id car_id INT DEFAULT NULL, CHANGE value value INT NOT NULL');
        $this->addSql('ALTER TABLE manufacturer DROP item_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE bitoriginal bitoriginal TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE part DROP item_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE manufacturer_id manufacturer_id INT DEFAULT NULL, CHANGE negative negative TINYINT(1) DEFAULT NULL, CHANGE fractional fractional TINYINT(1) DEFAULT NULL, CHANGE reserved reserved INT NOT NULL, CHANGE partnumber partnumber VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE partitem DROP move_motion_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE jobitem_id job_item_id INT DEFAULT NULL, CHANGE part_id part_id INT DEFAULT NULL, CHANGE is_order is_order TINYINT(1) DEFAULT NULL, CHANGE qty qty NUMERIC(5, 1) NOT NULL, CHANGE _order_id order_id INT DEFAULT NULL, CHANGE jobadvice_id job_advice_id INT DEFAULT NULL, CHANGE motion_id motion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE jobitem DROP employee__user_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE _user_id user_id INT DEFAULT NULL, CHANGE _order_id order_id INT DEFAULT NULL, CHANGE jobadvice_id job_advice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE jobadvice DROP item_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE car_id car_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE _order DROP ownedsecurableitem_id, DROP refs, DROP mileage_id, DROP eid, DROP paypoints, DROP bonus, DROP points, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE car_id car_id INT DEFAULT NULL, CHANGE client_id client_id INT DEFAULT NULL, CHANGE checkpay checkpay TINYINT(1) DEFAULT NULL, CHANGE paycard paycard INT DEFAULT NULL');
        $this->addSql('ALTER TABLE note DROP activity_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE order_id order_id INT DEFAULT NULL, CHANGE occurredondatetime created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE payment DROP item_id, DROP agent_client_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE motion DROP item_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE part_id part_item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE _user CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE person_id person_id INT DEFAULT NULL, DROP permitable_id, DROP hash, DROP language, DROP timezone, DROP username, DROP serializedavatardata, DROP manager__user_id, DROP role_id, DROP currency_id, DROP isactive');
        $this->addSql('ALTER TABLE person DROP department, DROP jobtitle, DROP officefax, DROP ownedsecurableitem_id, DROP title_ownedcustomfield_id, DROP title_customfield_id, DROP primaryemail_email_id, DROP primaryaddress_address_id, CHANGE mobilephone telephone VARCHAR(24) DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE officephone office_phone VARCHAR(24) DEFAULT NULL');
        $this->addSql('ALTER TABLE client DROP eid, DROP referal_client_id, DROP ref_bonus, DROP point_id, DROP ratio, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE person_id person_id INT DEFAULT NULL, CHANGE wallet wallet INT NOT NULL');
        $this->addSql('ALTER TABLE filecontent DROP meta_filemodel_id, DROP ext, DROP filemodel_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE order_id order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE keys_price CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE cnt cnt TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE cache_price CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE term term TINYINT(1) DEFAULT NULL, CHANGE id_price id_price INT DEFAULT NULL, CHANGE id_d2m id_d2m INT DEFAULT NULL, CHANGE qty qty INT DEFAULT NULL, CHANGE prc_ok prc_ok TINYINT(1) DEFAULT NULL, CHANGE min_qty min_qty INT DEFAULT NULL, CHANGE type_cross type_cross TINYINT(1) DEFAULT NULL, CHANGE qid qid INT DEFAULT NULL, CHANGE grp_part grp_part TINYINT(1) DEFAULT NULL');

        $this->addSql('RENAME TABLE _order TO orders');
        $this->addSql('RENAME TABLE _user TO users');
        $this->addSql('RENAME TABLE carmake TO car_manufacturer');
        $this->addSql('RENAME TABLE carmodel TO car_model');
        $this->addSql('RENAME TABLE cargeneration TO car_generation');
        $this->addSql('RENAME TABLE carmodification TO car_modification');
        $this->addSql('RENAME TABLE filecontent TO order_report');
        $this->addSql('RENAME TABLE jobadvice TO job_advice');
        $this->addSql('RENAME TABLE jobitem TO job_item');
        $this->addSql('RENAME TABLE partitem TO part_item');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_64D359345E237E06 ON car_manufacturer (name)');
        $this->addSql('ALTER TABLE car_generation ADD CONSTRAINT FK_E1F9E22A5E96AD46 FOREIGN KEY (carmodel_id) REFERENCES car_model (id)');
        $this->addSql('CREATE INDEX IDX_E1F9E22A5E96AD46 ON car_generation (carmodel_id)');
        $this->addSql('ALTER TABLE mileage ADD CONSTRAINT FK_56BDF8148D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE mileage ADD CONSTRAINT FK_56BDF814C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('CREATE INDEX IDX_56BDF8148D9F6D38 ON mileage (order_id)');
        $this->addSql('CREATE INDEX IDX_56BDF814C3C6F69F ON mileage (car_id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEC3C6F69F ON orders (car_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE19EB6921 ON orders (client_id)');
        $this->addSql('ALTER TABLE part ADD CONSTRAINT FK_490F70C6A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id)');
        $this->addSql('CREATE INDEX IDX_490F70C6A23B42D ON part (manufacturer_id)');
        $this->addSql('CREATE UNIQUE INDEX part_idx ON part (partnumber, manufacturer_id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_6D28840D19EB6921 ON payment (client_id)');
        $this->addSql('ALTER TABLE order_report ADD CONSTRAINT FK_7A067518D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_7A067518D9F6D38 ON order_report (order_id)');
        $this->addSql('ALTER TABLE car_modification ADD CONSTRAINT FK_B6BD9A3A56B8B385 FOREIGN KEY (cargeneration_id) REFERENCES car_generation (id)');
        $this->addSql('CREATE INDEX IDX_B6BD9A3A56B8B385 ON car_modification (cargeneration_id)');
        $this->addSql('ALTER TABLE motion ADD CONSTRAINT FK_F5FEA1E89233A555 FOREIGN KEY (part_item_id) REFERENCES part_item (id)');
        $this->addSql('CREATE INDEX IDX_F5FEA1E89233A555 ON motion (part_item_id)');
        $this->addSql('ALTER TABLE job_advice ADD CONSTRAINT FK_3486230CC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('CREATE INDEX IDX_3486230CC3C6F69F ON job_advice (car_id)');
        $this->addSql('ALTER TABLE job_item ADD CONSTRAINT FK_98D7535FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE job_item ADD CONSTRAINT FK_98D7535F8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_98D7535FA76ED395 ON job_item (user_id)');
        $this->addSql('CREATE INDEX IDX_98D7535F8D9F6D38 ON job_item (order_id)');
        $this->addSql('CREATE INDEX IDX_98D7535FD5296D14 ON job_item (job_advice_id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9217BBB47 ON users (person_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3D0AE6DC6D5321E ON manufacturer (logoad)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3D0AE6DC1B654FBE ON manufacturer (logoem)');
        $this->addSql('CREATE INDEX lastname_idx ON person (lastname)');
        $this->addSql('CREATE INDEX firstname_idx ON person (firstname)');
        $this->addSql('CREATE INDEX phone_idx ON person (telephone)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA148D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_CFBDFA148D9F6D38 ON note (order_id)');
        $this->addSql('ALTER TABLE car_model ADD CONSTRAINT FK_83EF70E2EE4789A FOREIGN KEY (carmake_id) REFERENCES car_manufacturer (id)');
        $this->addSql('CREATE INDEX IDX_83EF70E2EE4789A ON car_model (carmake_id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455217BBB47 ON client (person_id)');
        $this->addSql('ALTER TABLE car ADD created_at DATETIME NOT NULL, DROP creted_at');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D3180ED6B FOREIGN KEY (car_manufacturer_id) REFERENCES car_manufacturer (id)');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69DF64382E3 FOREIGN KEY (car_model_id) REFERENCES car_model (id)');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69DB1085141 ON car (vin)');
        $this->addSql('CREATE INDEX IDX_773DE69D3180ED6B ON car (car_manufacturer_id)');
        $this->addSql('CREATE INDEX IDX_773DE69DF64382E3 ON car (car_model_id)');
        $this->addSql('CREATE INDEX IDX_773DE69D71C30861 ON car (car_modification_id)');
        $this->addSql('CREATE INDEX IDX_773DE69D19EB6921 ON car (client_id)');
        $this->addSql('CREATE INDEX IDX_65B35B934CE34BEC ON part_item (part_id)');
        $this->addSql('CREATE INDEX IDX_65B35B938D9F6D38 ON part_item (order_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
