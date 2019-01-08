<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190108163043 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf('tenant' !== $this->connection->getDatabase());

        $this->addSql('ALTER TABLE car_note DROP FOREIGN KEY FK_4D7EEB8C3C6F69F');
        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_3486230CC3C6F69F');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69DF64382E3');
        $this->addSql('ALTER TABLE mc_equipment DROP FOREIGN KEY FK_793047587975B7E7');
        $this->addSql('ALTER TABLE car_recommendation_part DROP FOREIGN KEY FK_DDC72D65D173940B');
        $this->addSql('ALTER TABLE car_model DROP FOREIGN KEY FK_83EF70E2EE4789A');
        $this->addSql('ALTER TABLE part DROP FOREIGN KEY FK_490F70C6A23B42D');
        $this->addSql('ALTER TABLE mc_line DROP FOREIGN KEY FK_B37EBC5F517FE9FE');
        $this->addSql('ALTER TABLE mc_part DROP FOREIGN KEY FK_2B65786F4D7B7542');
        $this->addSql('ALTER TABLE mc_line DROP FOREIGN KEY FK_B37EBC5FBB3453DB');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D7E3C61F9');
        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_8E4BAAF26B20BA36');
        $this->addSql('ALTER TABLE operand_note DROP FOREIGN KEY FK_36BDE44118D7F226');
        $this->addSql('ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637CBF396750');
        $this->addSql('ALTER TABLE partner_operand DROP FOREIGN KEY FK_440A5FFB18D7F226');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176BF396750');
        $this->addSql('ALTER TABLE car_recommendation_part DROP FOREIGN KEY FK_DDC72D654CE34BEC');
        $this->addSql('ALTER TABLE mc_part DROP FOREIGN KEY FK_2B65786F4CE34BEC');
        $this->addSql('ALTER TABLE part_cross_part DROP FOREIGN KEY FK_B98F499C4CE34BEC');
        $this->addSql('ALTER TABLE part_cross_part DROP FOREIGN KEY FK_B98F499C70B9088C');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9217BBB47');
        $this->addSql('ALTER TABLE car_note DROP FOREIGN KEY FK_4D7EEB8B03A8386');
        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_8E4BAAF2B03A8386');
        $this->addSql('ALTER TABLE car_recommendation_part DROP FOREIGN KEY FK_DDC72D65B03A8386');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7B03A8386');
        $this->addSql('ALTER TABLE operand_note DROP FOREIGN KEY FK_36BDE441B03A8386');
        $this->addSql('ALTER TABLE user_credentials DROP FOREIGN KEY FK_531EE19BA76ED395');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE car_model');
        $this->addSql('DROP TABLE car_note');
        $this->addSql('DROP TABLE car_recommendation');
        $this->addSql('DROP TABLE car_recommendation_part');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE manufacturer');
        $this->addSql('DROP TABLE mc_equipment');
        $this->addSql('DROP TABLE mc_line');
        $this->addSql('DROP TABLE mc_part');
        $this->addSql('DROP TABLE mc_work');
        $this->addSql('DROP TABLE operand');
        $this->addSql('DROP TABLE operand_note');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE part');
        $this->addSql('DROP TABLE part_cross');
        $this->addSql('DROP TABLE part_cross_part');
        $this->addSql('DROP TABLE partner_operand');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE user_credentials');
        $this->addSql('DROP TABLE users');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(true, 'Rollback impossible');
    }
}
