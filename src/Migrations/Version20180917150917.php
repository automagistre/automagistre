<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180917150917 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item_part DROP FOREIGN KEY FK_3DB84FC5706C1B43');
        $this->addSql('DROP INDEX IDX_3DB84FC5706C1B43 ON order_item_part');
        $this->addSql('ALTER TABLE order_item_part DROP selector_id');
        $this->addSql('ALTER TABLE car_recommendation ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car_recommendation ADD CONSTRAINT FK_8E4BAAF2B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_8E4BAAF2B03A8386 ON car_recommendation (created_by_id)');
        $this->addSql('ALTER TABLE car_recommendation_part DROP FOREIGN KEY FK_DDC72D65706C1B43');
        $this->addSql('DROP INDEX IDX_DDC72D65706C1B43 ON car_recommendation_part');
        $this->addSql('ALTER TABLE car_recommendation_part CHANGE selector_id created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car_recommendation_part ADD CONSTRAINT FK_DDC72D65B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_DDC72D65B03A8386 ON car_recommendation_part (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car_recommendation DROP FOREIGN KEY FK_8E4BAAF2B03A8386');
        $this->addSql('DROP INDEX IDX_8E4BAAF2B03A8386 ON car_recommendation');
        $this->addSql('ALTER TABLE car_recommendation DROP created_by_id');
        $this->addSql('ALTER TABLE car_recommendation_part DROP FOREIGN KEY FK_DDC72D65B03A8386');
        $this->addSql('DROP INDEX IDX_DDC72D65B03A8386 ON car_recommendation_part');
        $this->addSql('ALTER TABLE car_recommendation_part CHANGE created_by_id selector_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car_recommendation_part ADD CONSTRAINT FK_DDC72D65706C1B43 FOREIGN KEY (selector_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_DDC72D65706C1B43 ON car_recommendation_part (selector_id)');
        $this->addSql('ALTER TABLE order_item_part ADD selector_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item_part ADD CONSTRAINT FK_3DB84FC5706C1B43 FOREIGN KEY (selector_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_3DB84FC5706C1B43 ON order_item_part (selector_id)');
    }
}
