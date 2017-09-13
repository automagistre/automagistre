<?php

declare(strict_types=1);

namespace App\Migrations;

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
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX search ON note (activity_id)');
        $this->addSql('CREATE INDEX search ON securableitem (item_id)');
        $this->addSql('CREATE INDEX search ON ownedsecurableitem (securableitem_id)');
        $this->addSql('CREATE INDEX search ON _order (ownedsecurableitem_id)');
        $this->addSql('CREATE INDEX search ON filemodel (filecontent_id)');
        $this->addSql('CREATE INDEX search2 ON filemodel (relatedmodel_id)');

        $this->addSql('ALTER TABLE `_order` MODIFY id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE carmake ENGINE = InnoDB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE carmodel CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE carmodification ENGINE = InnoDB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE cargeneration ENGINE = InnoDB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci');

        $this->addSql('OPTIMIZE TABLE note, securableitem, ownedsecurableitem, _order, filemodel');

        $this->addSql('ALTER TABLE note 
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            ADD order_id INT DEFAULT NULL,
            CHANGE occurredondatetime created_at DATETIME NOT NULL
        ');

        /* Link note to orders */
        $this->addSql('UPDATE note
            JOIN activity ON activity.id = note.activity_id
            JOIN activity_item ON activity_item.activity_id = activity.id
            JOIN securableitem ON securableitem.item_id = activity_item.item_id
            JOIN ownedsecurableitem ON ownedsecurableitem.securableitem_id = securableitem.id
            JOIN _order ON _order.ownedsecurableitem_id = ownedsecurableitem.id
            SET note.order_id = _order.id
        ');

        $this->addSql('DELETE FROM note WHERE order_id IS NULL OR description = \'Сформирован отчет\'');
        $this->addSql('ALTER TABLE note 
            ADD CONSTRAINT FK_CFBDFA148D9F6D38 FOREIGN KEY (order_id) REFERENCES `_order` (id),
            DROP activity_id
        ');

        /* Link orders report to orders */
        $this->addSql('ALTER TABLE filecontent ADD order_id INT DEFAULT NULL');

        $this->addSql('UPDATE filecontent
            JOIN filemodel ON filemodel.filecontent_id = filecontent.id
            JOIN note ON note.id = filemodel.relatedmodel_id
            SET filecontent.order_id = note.order_id
        ');

        $this->addSql('UPDATE filecontent
            JOIN filemodel ON filemodel.filecontent_id = filecontent.id
            JOIN `_order` ON substr(filemodel.name, 10, 4) = `_order`.id
            SET filecontent.order_id = `_order`.id
            WHERE filecontent.order_id IS NULL
        ');

        $this->addSql('ALTER TABLE filecontent 
            MODIFY order_id INT NOT NULL,
            ADD CONSTRAINT FK_7A067518D9F6D38 FOREIGN KEY (order_id) REFERENCES `_order` (id)
        ');

        /* Set mileage to order table as integer */
        $this->addSql('ALTER TABLE `_order` ADD mileage INT(8) UNSIGNED DEFAULT NULL');
        $this->addSql('UPDATE `_order` o
              LEFT JOIN mileage m ON m.id = o.mileage_id
            SET o.mileage = m.value;            
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

        /* Delete PartItem which linked to deleted Parts */
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

        /* Insert all carmake to manufacturer */
        $this->addSql('INSERT INTO manufacturer (name)
            SELECT carmake.name
            FROM carmake
            LEFT JOIN manufacturer ON carmake.name = manufacturer.name
            WHERE manufacturer.id IS NULL
            GROUP BY carmake.name
        ');

        /* Update carmodel, replace carmake_id by manufacturer_id */
        $this->addSql('
            UPDATE carmodel
                LEFT JOIN carmake ON carmake.id = carmodel.carmake_id
                LEFT JOIN manufacturer ON carmake.name = manufacturer.name
            SET carmodel.carmake_id = (manufacturer.id);
        ');

        /* Delete motions which linked to deleted parts */
        $this->addSql('DELETE motion FROM motion
            LEFT JOIN part ON part.id = motion.part_id
            WHERE part.id IS NULL
        ');

        /* Format telephones */
        $this->addSql('UPDATE person SET mobilephone = replace(replace(replace(replace(replace(TRIM(replace(mobilephone, CHAR(9), \'\')), \'-\', \'\'), \')\', \'\'), \'(\', \'\'), \'+7\', \'\'), \' \', \'\')');
        $this->addSql('UPDATE person SET mobilephone = SUBSTRING(mobilephone, 2) WHERE 11 = LENGTH(mobilephone) AND mobilephone LIKE \'8%\'');
        $this->addSql('UPDATE person SET mobilephone = NULL WHERE 0 = LENGTH(mobilephone)');
        $this->addSql('UPDATE person SET officephone = NULL WHERE 0 = LENGTH(officephone)');
        $this->addSql('UPDATE person SET mobilephone = concat(\'495\', mobilephone) WHERE 7 = LENGTH(mobilephone)');

        $this->addSql('
            UPDATE _order
            SET status = CASE
                         WHEN status = \'swOrder/draft\'
                           THEN 1
                         WHEN status = \'swOrder/scheduling\'
                           THEN 2
                         WHEN status = \'swOrder/ordering\'
                           THEN 3
                         WHEN status = \'swOrder/matching\'
                           THEN 4
                         WHEN status = \'swOrder/tracking\'
                           THEN 5
                         WHEN status = \'swOrder/delivery\'
                           THEN 6
                         WHEN status = \'swOrder/notification\'
                           THEN 7
                         WHEN status = \'swOrder/working\'
                           THEN 8
                         WHEN status = \'swOrder/ready\'
                           THEN 9
                         WHEN status = \'swOrder/closed\'
                           THEN 10
                         END;
        ');

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
        $this->addSql('DROP TABLE carmake');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE customfield');
        $this->addSql('DROP TABLE dashboard');
        $this->addSql('DROP TABLE derivedattributemetadata');
        $this->addSql('DROP TABLE diagrecord');
        $this->addSql('DROP TABLE dropdowndependencyderivedattributemetadata');
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
        $this->addSql('DROP TABLE mileage');
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

        $this->addSql('RENAME TABLE _order TO orders');
        $this->addSql('RENAME TABLE _user TO users');
        $this->addSql('RENAME TABLE carmodel TO car_model');
        $this->addSql('RENAME TABLE cargeneration TO car_generation');
        $this->addSql('RENAME TABLE carmodification TO car_modification');
        $this->addSql('RENAME TABLE filecontent TO order_report');
        $this->addSql('RENAME TABLE jobadvice TO job_advice');

        $this->addSql('DROP INDEX IDX_GOSNOMER ON car');
        $this->addSql('DROP INDEX sprite_id ON car');
        $this->addSql('DROP INDEX idx_car_client ON car');
        $this->addSql('DROP INDEX uq_vin ON car');
        $this->addSql('DROP INDEX UQ_3a7293440d99b39c56ff99074677931de71144cb ON car');
        $this->addSql('DROP INDEX fk_am_models_am_makes ON car_model');
        $this->addSql('DROP INDEX idx_modif_parent ON car_modification');
        $this->addSql('DROP INDEX idx_order_car ON orders');
        $this->addSql('DROP INDEX idx_order_client ON orders');
        $this->addSql('DROP INDEX part_uniq ON part');
        $this->addSql('DROP INDEX uq_2f9f20ae60de87f7bdd974b52941c30e287c6eef ON users');
        $this->addSql('DROP INDEX idx_generat_parent ON car_generation');
        $this->addSql('DROP INDEX EID_IDX ON client');
        $this->addSql('DROP INDEX IDX_CLIENT_PERSON ON client');
        $this->addSql('DROP INDEX uq_c17cf66b38ac3bd928a6ebf320320881ce022754 ON manufacturer');
        $this->addSql('DROP INDEX uq_e866d1f7bc3130384a2fd1ad1ddd50921a0101b9 ON manufacturer');
        $this->addSql('DROP INDEX idx_person_lastname ON person');
        $this->addSql('DROP INDEX idx_person_firstname ON person');
        $this->addSql('DROP INDEX idx_person_phone ON person');

        $this->addSql('ALTER TABLE manufacturer 
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            DROP item_id,
            DROP bitoriginal,
            DROP logoad,
            DROP logoem,
            DROP logopl
        ');

        $this->addSql('ALTER TABLE car_model 
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            MODIFY COLUMN name VARCHAR(30) NOT NULL,
            CHANGE carmake_id manufacturer_id INT NOT NULL,
            DROP folder,
            DROP link,
            DROP loaded,
            ADD CONSTRAINT FK_83EF70E2EE4789A FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id)
        ');

        $this->addSql('ALTER TABLE car_generation 
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            CHANGE carmodel_id car_model_id INT NOT NULL,
            DROP folder,
            ADD CONSTRAINT FK_E1F9E22A5E96AD46 FOREIGN KEY (car_model_id) REFERENCES car_model (id)
        ');

        $this->addSql('ALTER TABLE car_modification
            ADD `case` SMALLINT DEFAULT NULL,
            ADD `engine` VARCHAR(255) DEFAULT NULL,
            ADD `transmission` SMALLINT DEFAULT NULL,
            ADD `wheel_drive` SMALLINT DEFAULT NULL,
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            MODIFY COLUMN name VARCHAR(30) DEFAULT NULL,
            MODIFY COLUMN hp SMALLINT DEFAULT NULL,
            MODIFY COLUMN doors SMALLINT DEFAULT NULL,
            MODIFY COLUMN `from` SMALLINT DEFAULT NULL,
            MODIFY COLUMN till SMALLINT DEFAULT NULL,
            MODIFY COLUMN tank SMALLINT DEFAULT NULL,
            CHANGE cargeneration_id car_generation_id INT NOT NULL,
            DROP folder,
            DROP link,
            ADD CONSTRAINT FK_B6BD9A3A56B8B385 FOREIGN KEY (car_generation_id) REFERENCES car_generation (id)
        ');

        $this->addSql('ALTER TABLE person 
            ADD email VARCHAR(255) DEFAULT NULL,
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            CHANGE mobilephone telephone VARCHAR(24) DEFAULT NULL,
            CHANGE officephone office_phone VARCHAR(24) DEFAULT NULL,
            DROP department,
            DROP jobtitle,
            DROP officefax,
            DROP ownedsecurableitem_id,
            DROP title_ownedcustomfield_id,
            DROP title_customfield_id,
            DROP primaryaddress_address_id
        ');
        $this->addSql('UPDATE person LEFT JOIN email ON email.id = person.primaryemail_email_id SET person.email = email.emailaddress');
        $this->addSql('DROP TABLE email');
        $this->addSql('ALTER TABLE person DROP primaryemail_email_id');

        $this->addSql('ALTER TABLE client
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            CHANGE person_id person_id INT DEFAULT NULL,
            CHANGE wallet wallet INT NOT NULL, 
            DROP eid,
            DROP referal_client_id,
            DROP ref_bonus,
            DROP point_id,
            ADD CONSTRAINT FK_C7440455217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)
        ');

        $this->addSql('ALTER TABLE car 
            ADD created_at DATETIME NOT NULL,
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            MODIFY COLUMN `year` INT DEFAULT NULL,
            MODIFY COLUMN client_id INT DEFAULT NULL,
            CHANGE carmodel_id car_model_id INT DEFAULT NULL,
            CHANGE carmodification_id car_modification_id INT DEFAULT NULL,
            DROP item_id,
            DROP mileage_id,
            DROP eid,
            DROP cargeneration_id,
            DROP make_carmake_id,
            DROP model_carmodel_id,
            DROP modification_carmodification_id,
            DROP carmake_id,
            ADD CONSTRAINT FK_773DE69DF64382E3 FOREIGN KEY (car_model_id) REFERENCES car_model (id),
            ADD CONSTRAINT FK_773DE69D19EB6921 FOREIGN KEY (client_id) REFERENCES client (id),
            ADD CONSTRAINT FK_773DE69D71C30861 FOREIGN KEY (car_modification_id) REFERENCES car_modification (id)');
        $this->addSql('UPDATE car c
            JOIN (SELECT COALESCE(o.startdate, o.closeddate) AS created_at, o.car_id FROM orders o GROUP BY o.car_id ORDER BY o.id ASC) t ON c.id = t.car_id
            SET c.created_at = t.created_at
            WHERE c.created_at IS NULL');
        $this->addSql('UPDATE car SET created_at = CURRENT_TIMESTAMP() WHERE created_at = \'0000-00-00 00:00:00\'');

        $this->addSql('ALTER TABLE part 
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            MODIFY COLUMN manufacturer_id INT DEFAULT NULL,
            MODIFY COLUMN negative TINYINT(1) DEFAULT NULL,
            MODIFY COLUMN fractional TINYINT(1) DEFAULT NULL,
            MODIFY COLUMN reserved INT NOT NULL,
            MODIFY COLUMN partnumber VARCHAR(30) NOT NULL,
            DROP item_id,
            ADD CONSTRAINT FK_490F70C6A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id)
        ');

        $this->addSql('ALTER TABLE job_advice 
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            MODIFY COLUMN car_id INT DEFAULT NULL,
            DROP item_id,
            ADD CONSTRAINT FK_3486230CC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)
        ');

        $this->addSql('ALTER TABLE orders
            MODIFY COLUMN car_id INT DEFAULT NULL,
            MODIFY COLUMN client_id INT DEFAULT NULL,
            MODIFY COLUMN checkpay TINYINT(1) DEFAULT NULL,
            MODIFY COLUMN paycard INT DEFAULT NULL, 
            MODIFY COLUMN status SMALLINT NOT NULL,
            DROP mileage_id,
            DROP ownedsecurableitem_id,
            DROP refs,
            DROP eid,
            DROP paypoints,
            DROP bonus,
            DROP points,
            ADD CONSTRAINT FK_E52FFDEEC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id),
            ADD CONSTRAINT FK_E52FFDEE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)
        ');

        $this->addSql('ALTER TABLE payment 
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            MODIFY COLUMN client_id INT DEFAULT NULL,
            DROP item_id,
            DROP agent_client_id,
            ADD CONSTRAINT FK_6D28840D19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)
        ');

        $this->addSql('ALTER TABLE motion 
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            MODIFY COLUMN part_id INT DEFAULT NULL,
            DROP item_id,
            ADD CONSTRAINT FK_F5FEA1E89233A555 FOREIGN KEY (part_id) REFERENCES part (id)
        ');

        $this->addSql('ALTER TABLE users 
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            MODIFY COLUMN person_id INT DEFAULT NULL,
            DROP permitable_id,
            DROP hash,
            DROP language,
            DROP timezone,
            DROP username,
            DROP serializedavatardata,
            DROP manager__user_id,
            DROP role_id,
            DROP currency_id,
            DROP isactive,
            ADD CONSTRAINT FK_1483A5E9217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)
        ');

        $this->addSql('ALTER TABLE order_report 
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            MODIFY COLUMN order_id INT DEFAULT NULL,
            DROP meta_filemodel_id,
            DROP ext,
            DROP filemodel_id
        ');

        $this->addSql('ALTER TABLE keys_price 
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            MODIFY COLUMN cnt TINYINT(1) DEFAULT NULL
        ');

        $this->addSql('ALTER TABLE cache_price 
            MODIFY COLUMN id INT AUTO_INCREMENT NOT NULL,
            MODIFY COLUMN term TINYINT(1) DEFAULT NULL,
            CHANGE id_price id_price INT DEFAULT NULL,
            CHANGE id_d2m id_d2m INT DEFAULT NULL,
            CHANGE qty qty INT DEFAULT NULL,
            CHANGE prc_ok prc_ok TINYINT(1) DEFAULT NULL,
            CHANGE min_qty min_qty INT DEFAULT NULL,
            CHANGE type_cross type_cross TINYINT(1) DEFAULT NULL,
            CHANGE qid qid INT DEFAULT NULL,
            CHANGE grp_part grp_part TINYINT(1) DEFAULT NULL');

        $this->addSql('CREATE TABLE service (
            id   INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255)       NOT NULL,
            UNIQUE INDEX UNIQ_E19D9AD25E237E06 (name),
            PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE order_service (
            id INT AUTO_INCREMENT NOT NULL,
            order_id INT DEFAULT NULL,
            service_id INT DEFAULT NULL,
            user_id INT DEFAULT NULL,
            cost INT NOT NULL, 
            INDEX IDX_17E733998D9F6D38 (order_id), 
            INDEX IDX_17E73399ED5CA9E6 (service_id), 
            INDEX IDX_17E73399A76ED395 (user_id),
            FOREIGN KEY FK_17E733998D9F6D38 (order_id) REFERENCES orders (id),
            FOREIGN KEY FK_17E73399ED5CA9E6 (service_id) REFERENCES service (id),
            FOREIGN KEY FK_17E73399A76ED395 (user_id) REFERENCES users (id),               
            PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE order_part (
            id INT AUTO_INCREMENT NOT NULL,
            order_id INT DEFAULT NULL,
            part_id INT DEFAULT NULL,
            quantity INT NOT NULL,
            cost INT NOT NULL,
            order_service_id  INT DEFAULT NULL,
            INDEX IDX_4FE4AD18D9F6D38 (order_id),
            INDEX IDX_4FE4AD14CE34BEC (part_id),
            INDEX IDX_4FE4AD15E8654B3 (order_service_id), 
            FOREIGN KEY FK_4FE4AD18D9F6D38 (order_id) REFERENCES orders (id),
            FOREIGN KEY FK_4FE4AD14CE34BEC (part_id) REFERENCES part (id),
            FOREIGN KEY FK_4FE4AD1ED5CA9E6 (order_service_id) REFERENCES order_service (id),
            PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('INSERT INTO service (name)
            SELECT DISTINCT name FROM jobitem');

        $this->addSql('INSERT INTO order_service (order_id, service_id, user_id, cost)
            SELECT j.`_order_id`, service.id, j.`_user_id`, j.cost FROM jobitem j
            LEFT JOIN service ON j.name = service.name');

        $this->addSql('INSERT INTO order_part (order_id, part_id, quantity, cost, order_service_id)
            SELECT p.`_order_id`, p.part_id, p.qty, COALESCE(p.cost, 0), order_service.id FROM partitem p
            LEFT JOIN jobitem ON jobitem.id = p.jobitem_id
            LEFT JOIN service ON service.name = jobitem.name
            LEFT JOIN order_service ON order_service.order_id = p.`_order_id` AND order_service.service_id = service.id 
        ');

        $this->addSql('DROP TABLE jobitem');
        $this->addSql('DROP TABLE partitem');

        $this->addSql('CREATE INDEX IDX_83EF70EA23B42D ON car_model (manufacturer_id)');
        $this->addSql('CREATE INDEX IDX_E1F9E22AF64382E3 ON car_generation (car_model_id)');
        $this->addSql('CREATE INDEX IDX_B6BD9A3AFBB2DD31 ON car_modification (car_generation_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEC3C6F69F ON orders (car_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE19EB6921 ON orders (client_id)');
        $this->addSql('CREATE INDEX IDX_490F70C6A23B42D ON part (manufacturer_id)');
        $this->addSql('CREATE UNIQUE INDEX part_idx ON part (partnumber, manufacturer_id)');
        $this->addSql('CREATE INDEX IDX_6D28840D19EB6921 ON payment (client_id)');
        $this->addSql('CREATE INDEX IDX_7A067518D9F6D38 ON order_report (order_id)');
        $this->addSql('CREATE INDEX IDX_F5FEA1E84CE34BEC ON motion (part_id)');
        $this->addSql('CREATE INDEX IDX_3486230CC3C6F69F ON job_advice (car_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9217BBB47 ON users (person_id)');
        $this->addSql('CREATE INDEX lastname_idx ON person (lastname)');
        $this->addSql('CREATE INDEX firstname_idx ON person (firstname)');
        $this->addSql('CREATE INDEX phone_idx ON person (telephone)');
        $this->addSql('CREATE INDEX IDX_CFBDFA148D9F6D38 ON note (order_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455217BBB47 ON client (person_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69DB1085141 ON car (vin)');
        $this->addSql('CREATE INDEX IDX_773DE69DF64382E3 ON car (car_model_id)');
        $this->addSql('CREATE INDEX IDX_773DE69D71C30861 ON car (car_modification_id)');
        $this->addSql('CREATE INDEX IDX_773DE69D19EB6921 ON car (client_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }
}
