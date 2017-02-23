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

        $this->addSql('DROP TABLE activelanguage');
        $this->addSql('DROP TABLE actual_permissions_cache');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE basecustomfield');
        $this->addSql('DROP TABLE calculatedderivedattributemetadata');
        $this->addSql('DROP TABLE customfield');
        $this->addSql('DROP TABLE dashboard');
        $this->addSql('DROP TABLE derivedattributemetadata');
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
        $this->addSql('DROP TABLE globalmetadata');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE messagetranslation');
        $this->addSql('DROP TABLE note_read');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notificationmessage');
        $this->addSql('DROP TABLE ownedcustomfield');
        $this->addSql('DROP TABLE perusermetadata');
        $this->addSql('DROP TABLE portlet');
        $this->addSql('DROP TABLE savedsearch');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE activelanguage (id INT UNSIGNED AUTO_INCREMENT NOT NULL, code VARCHAR(16) DEFAULT NULL COLLATE utf8_unicode_ci, name VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, nativename VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, activationdatetime DATETIME DEFAULT NULL, lastupdatedatetime DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE actual_permissions_cache (securableitem_id INT UNSIGNED NOT NULL, permitable_id INT UNSIGNED NOT NULL, allow_permissions TINYINT(1) NOT NULL, deny_permissions TINYINT(1) NOT NULL, PRIMARY KEY(securableitem_id, permitable_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE address (id INT UNSIGNED AUTO_INCREMENT NOT NULL, city VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, country VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, invalid TINYINT(1) DEFAULT NULL, postalcode VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, street1 VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, street2 VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, state VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE basecustomfield (id INT UNSIGNED AUTO_INCREMENT NOT NULL, data_customfielddata_id LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:simple_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE calculatedderivedattributemetadata (id INT UNSIGNED AUTO_INCREMENT NOT NULL, derivedattributemetadata_id INT UNSIGNED DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customfield (id INT UNSIGNED AUTO_INCREMENT NOT NULL, basecustomfield_id INT UNSIGNED DEFAULT NULL, value TEXT DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dashboard (id INT UNSIGNED AUTO_INCREMENT NOT NULL, ownedsecurableitem_id INT UNSIGNED DEFAULT NULL, isdefault TINYINT(1) DEFAULT NULL, layouttype VARCHAR(10) DEFAULT NULL COLLATE utf8_unicode_ci, name VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, layoutid INT UNSIGNED DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE derivedattributemetadata (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, modelclassname VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, serializedmetadata VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dropdowndependencyderivedattributemetadata (id INT AUTO_INCREMENT NOT NULL, derivedattributemetadata_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emailaccount (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT UNSIGNED DEFAULT NULL, _user_id INT UNSIGNED DEFAULT NULL, name TEXT DEFAULT NULL COLLATE utf8_unicode_ci, fromname VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, replytoname VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, outboundhost VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, outboundusername VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, outboundpassword VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, outboundsecurity VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, outboundtype VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, fromaddress VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, usecustomoutboundsettings TINYINT(1) DEFAULT NULL, outboundport TINYINT(1) DEFAULT NULL, replytoaddress LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:simple_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emailbox (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emailfolder (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT UNSIGNED DEFAULT NULL, emailbox_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emailmessage (id INT UNSIGNED AUTO_INCREMENT NOT NULL, ownedsecurableitem_id INT UNSIGNED DEFAULT NULL, content_emailmessagecontent_id INT UNSIGNED DEFAULT NULL, sender_emailmessagesender_id INT UNSIGNED DEFAULT NULL, subject TEXT DEFAULT NULL COLLATE utf8_unicode_ci, folder_emailfolder_id INT UNSIGNED DEFAULT NULL, error_emailmessagesenderror_id INT UNSIGNED DEFAULT NULL, sentdatetime DATETIME DEFAULT NULL, sendondatetime DATETIME DEFAULT NULL, sendattempts TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emailmessage_read (id INT AUTO_INCREMENT NOT NULL, munge_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emailmessagecontent (id INT UNSIGNED AUTO_INCREMENT NOT NULL, htmlcontent TEXT DEFAULT NULL COLLATE utf8_unicode_ci, textcontent TEXT DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emailmessagerecipient (id INT UNSIGNED AUTO_INCREMENT NOT NULL, personoraccount_item_id INT UNSIGNED DEFAULT NULL, toname VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, toaddress VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, type LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:simple_array)\', emailmessage_id INT UNSIGNED DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emailmessagesender (id INT UNSIGNED AUTO_INCREMENT NOT NULL, personoraccount_item_id INT UNSIGNED DEFAULT NULL, fromname VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, fromaddress VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emailmessagesenderror (id INT UNSIGNED AUTO_INCREMENT NOT NULL, createddatetime DATETIME DEFAULT NULL, serializeddata LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emailsignature (id INT UNSIGNED AUTO_INCREMENT NOT NULL, _user_id INT UNSIGNED DEFAULT NULL, htmlcontent TEXT DEFAULT NULL COLLATE utf8_unicode_ci, textcontent TEXT DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE globalmetadata (id INT UNSIGNED AUTO_INCREMENT NOT NULL, classname VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, serializedmetadata TEXT DEFAULT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UQ_6950932d5c0020179c0a175933c8d60ccab633ae (classname), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT UNSIGNED AUTO_INCREMENT NOT NULL, timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, message VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messagetranslation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, messagesource_id INT UNSIGNED DEFAULT NULL, translation BLOB DEFAULT NULL, language VARCHAR(16) DEFAULT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX source_language_translation_Index (messagesource_id, language, translation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note_read (id INT AUTO_INCREMENT NOT NULL, munge_id INT DEFAULT NULL, securableitem_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT UNSIGNED DEFAULT NULL, owner__user_id INT UNSIGNED DEFAULT NULL, notificationmessage_id INT UNSIGNED DEFAULT NULL, type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notificationmessage (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT UNSIGNED DEFAULT NULL, htmlcontent TEXT DEFAULT NULL COLLATE utf8_unicode_ci, textcontent TEXT DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ownedcustomfield (id INT UNSIGNED AUTO_INCREMENT NOT NULL, customfield_id INT UNSIGNED DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE perusermetadata (id INT UNSIGNED AUTO_INCREMENT NOT NULL, _user_id INT UNSIGNED DEFAULT NULL, classname VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, serializedmetadata TEXT DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE portlet (id INT UNSIGNED AUTO_INCREMENT NOT NULL, _user_id INT UNSIGNED DEFAULT NULL, layoutid VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci, viewtype TEXT DEFAULT NULL COLLATE utf8_unicode_ci, serializedviewdata TEXT DEFAULT NULL COLLATE utf8_unicode_ci, collapsed TINYINT(1) DEFAULT NULL, `column` LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:simple_array)\', position TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE savedsearch (id INT UNSIGNED AUTO_INCREMENT NOT NULL, ownedsecurableitem_id INT UNSIGNED DEFAULT NULL, name VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, serializeddata TEXT DEFAULT NULL COLLATE utf8_unicode_ci, viewclassname VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }
}
