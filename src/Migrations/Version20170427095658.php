<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170427095658 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_part DROP FOREIGN KEY FK_4FE4AD1ED5CA9E6');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, created_at DATETIME NOT NULL, type INT NOT NULL, INDEX IDX_52EA1F098D9F6D38 (order_id), INDEX IDX_52EA1F09727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item_service (id INT NOT NULL, service_id INT DEFAULT NULL, worker_id INT DEFAULT NULL, price INT NOT NULL, INDEX IDX_EE0028ECED5CA9E6 (service_id), INDEX IDX_EE0028EC6B20BA36 (worker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item_part (id INT NOT NULL, part_id INT DEFAULT NULL, selector_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', quantity INT NOT NULL, price INT NOT NULL, INDEX IDX_3DB84FC54CE34BEC (part_id), INDEX IDX_3DB84FC5706C1B43 (selector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item_group (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09727ACA70 FOREIGN KEY (parent_id) REFERENCES order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_item_service ADD CONSTRAINT FK_EE0028ECED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE order_item_service ADD CONSTRAINT FK_EE0028EC6B20BA36 FOREIGN KEY (worker_id) REFERENCES operand (id)');
        $this->addSql('ALTER TABLE order_item_service ADD CONSTRAINT FK_EE0028ECBF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_item_part ADD CONSTRAINT FK_3DB84FC54CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE order_item_part ADD CONSTRAINT FK_3DB84FC5706C1B43 FOREIGN KEY (selector_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE order_item_part ADD CONSTRAINT FK_3DB84FC5BF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_item_group ADD CONSTRAINT FK_F4BDA240BF396750 FOREIGN KEY (id) REFERENCES order_item (id) ON DELETE CASCADE');

        $this->addSql('INSERT INTO order_item (id, order_id, created_at, type)
            SELECT os.id, os.order_id, orders.created_at, 1 FROM  order_service os
            JOIN orders ON orders.id = os.order_id');
        $this->addSql('INSERT INTO order_item_service (id, service_id, worker_id, price)
            SELECT os.id, os.service_id, os.worker_id, os.cost FROM order_service os');
        $this->addSql('ALTER TABLE order_item ADD COLUMN part_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX search ON order_item (part_id)');
        $this->addSql('INSERT INTO order_item (order_id, parent_id, created_at, type, part_id)
            SELECT op.order_id, op.order_service_id, orders.created_at, 2, op.id FROM order_part op
            JOIN orders ON orders.id = op.order_id');
        $this->addSql('INSERT INTO order_item_part (id, part_id, selector_id, quantity, price)
            SELECT order_item.id, op.part_id, op.selector_id, op.quantity, op.cost FROM order_part op
            LEFT JOIN order_item ON order_item.part_id = op.id');
        $this->addSql('ALTER TABLE order_item DROP COLUMN part_id');

        $this->addSql('DROP TABLE order_part');
        $this->addSql('DROP TABLE order_service');
        $this->addSql('ALTER TABLE car_recommendation CHANGE cost price INT NOT NULL');
        $this->addSql('ALTER TABLE car_recommendation_part CHANGE cost price INT NOT NULL');

        $this->addSql('UPDATE order_item_service SET price = price * 100 WHERE price > 0');
        $this->addSql('UPDATE order_item_part SET price = price * 100 WHERE price > 0');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09727ACA70');
        $this->addSql('ALTER TABLE order_item_service DROP FOREIGN KEY FK_EE0028ECBF396750');
        $this->addSql('ALTER TABLE order_item_part DROP FOREIGN KEY FK_3DB84FC5BF396750');
        $this->addSql('ALTER TABLE order_item_group DROP FOREIGN KEY FK_F4BDA240BF396750');
        $this->addSql('CREATE TABLE order_part (id INT AUTO_INCREMENT NOT NULL, part_id INT DEFAULT NULL, selector_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', order_id INT DEFAULT NULL, order_service_id INT DEFAULT NULL, quantity INT NOT NULL, cost INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_4FE4AD18D9F6D38 (order_id), INDEX IDX_4FE4AD14CE34BEC (part_id), INDEX IDX_4FE4AD15E8654B3 (order_service_id), INDEX IDX_4FE4AD1706C1B43 (selector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_service (id INT AUTO_INCREMENT NOT NULL, worker_id INT DEFAULT NULL, order_id INT DEFAULT NULL, service_id INT DEFAULT NULL, cost INT NOT NULL, INDEX IDX_17E733998D9F6D38 (order_id), INDEX IDX_17E73399ED5CA9E6 (service_id), INDEX IDX_17E733996B20BA36 (worker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_part ADD CONSTRAINT FK_4FE4AD14CE34BEC FOREIGN KEY (part_id) REFERENCES part (id)');
        $this->addSql('ALTER TABLE order_part ADD CONSTRAINT FK_4FE4AD1706C1B43 FOREIGN KEY (selector_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE order_part ADD CONSTRAINT FK_4FE4AD18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_part ADD CONSTRAINT FK_4FE4AD1ED5CA9E6 FOREIGN KEY (order_service_id) REFERENCES order_service (id)');
        $this->addSql('ALTER TABLE order_service ADD CONSTRAINT FK_17E733996B20BA36 FOREIGN KEY (worker_id) REFERENCES operand (id)');
        $this->addSql('ALTER TABLE order_service ADD CONSTRAINT FK_17E733998D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_service ADD CONSTRAINT FK_17E73399ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE order_item_service');
        $this->addSql('DROP TABLE order_item_part');
        $this->addSql('DROP TABLE order_item_group');
        $this->addSql('ALTER TABLE car_recommendation CHANGE price cost INT NOT NULL');
        $this->addSql('ALTER TABLE car_recommendation_part CHANGE price cost INT NOT NULL');
    }
}
