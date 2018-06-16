<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170325195358 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE employee (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, ratio INT NOT NULL, hired_at DATETIME NOT NULL, fired_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_5D9F75A1217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operand (id INT AUTO_INCREMENT NOT NULL, type INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('INSERT INTO operand (id, type) SELECT id, 1 FROM person');
        $this->addSql('TRUNCATE organization');

        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('INSERT INTO employee (person_id, ratio, hired_at)
          SELECT
            person.id,
            client.ratio,
            NOW()
          FROM client
            LEFT JOIN person ON person.id = client.person_id
          WHERE client.employee IS TRUE');

        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE19EB6921');
        $this->addSql('DROP INDEX IDX_E52FFDEE19EB6921 ON orders');
        $this->addSql('DELETE FROM orders WHERE customer_type = 2');
        $this->addSql('ALTER TABLE orders DROP client_id, DROP customer_type');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE9395C3F3 FOREIGN KEY (customer_id) REFERENCES operand (id)');
        $this->addSql('ALTER TABLE order_service DROP FOREIGN KEY FK_17E73399A76ED395');
        $this->addSql('DROP INDEX IDX_17E73399A76ED395 ON order_service');
        $this->addSql('UPDATE order_service
            LEFT JOIN users ON users.id = order_service.user_id
            SET order_service.user_id = users.person_id');
        $this->addSql('ALTER TABLE order_service CHANGE user_id worker_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_service ADD CONSTRAINT FK_17E733996B20BA36 FOREIGN KEY (worker_id) REFERENCES operand (id)');
        $this->addSql('CREATE INDEX IDX_17E733996B20BA36 ON order_service (worker_id)');
        $this->addSql('ALTER TABLE organization CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637CBF396750 FOREIGN KEY (id) REFERENCES operand (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D19EB6921');
        $this->addSql('DROP INDEX IDX_6D28840D19EB6921 ON payment');
        $this->addSql('UPDATE payment LEFT JOIN client ON client.id = payment.client_id SET payment.client_id = client.person_id');
        $this->addSql('ALTER TABLE payment CHANGE client_id person_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_6D28840D217BBB47 ON payment (person_id)');

        $this->addSql('DROP INDEX sprite_id ON person');
        $this->addSql('DROP INDEX lastname_idx ON person');
        $this->addSql('DROP INDEX firstname_idx ON person');
        $this->addSql('DROP INDEX phone_idx ON person');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE9395C3F3');
        $this->addSql('ALTER TABLE order_service DROP FOREIGN KEY FK_17E733996B20BA36');
        $this->addSql('ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637CBF396750');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE operand');
        $this->addSql('DROP INDEX IDX_17E733996B20BA36 ON order_service');
        $this->addSql('ALTER TABLE order_service CHANGE worker_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_service ADD CONSTRAINT FK_17E73399A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_17E73399A76ED395 ON order_service (user_id)');
        $this->addSql('ALTER TABLE orders ADD client_id INT DEFAULT NULL, ADD customer_type INT NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE19EB6921 ON orders (client_id)');
        $this->addSql('ALTER TABLE organization CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D217BBB47');
        $this->addSql('DROP INDEX IDX_6D28840D217BBB47 ON payment');
        $this->addSql('ALTER TABLE payment CHANGE person_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_6D28840D19EB6921 ON payment (client_id)');
        $this->addSql('CREATE INDEX sprite_id ON person (sprite_id)');
        $this->addSql('CREATE INDEX lastname_idx ON person (lastname)');
        $this->addSql('CREATE INDEX firstname_idx ON person (firstname)');
        $this->addSql('CREATE INDEX phone_idx ON person (telephone)');
    }
}
