<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170325221423 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE organization ADD office_phone VARCHAR(255) DEFAULT NULL');

        $this->addSql('DELETE FROM employee WHERE person_id = 1 OR person_id = 3760');

        $this->addSql('CREATE TEMPORARY TABLE IF NOT EXISTS temp AS (
            SELECT *
            FROM person
            WHERE (firstname LIKE \'%ООО%\'
                 OR lastname LIKE \'%ООО%\'
                 OR lastname LIKE \'%ЗАО%\'
                 OR firstname LIKE \'%ЗАО%\'
                 OR lastname LIKE \'%ОАО%\'
                 OR firstname LIKE \'%ОАО%\'
                 OR lastname LIKE \'%ЧОП%\'
                 OR firstname LIKE \'%ЧОП%\'
                 OR lastname LIKE \'%ФГУП%\'
                 OR firstname LIKE \'%ФГУП%\'
                 OR lastname LIKE \'%"%\'
                 OR firstname LIKE \'%"%\'
                 OR lastname LIKE \'%»%\'
                 OR firstname LIKE \'%»%\'
                 OR lastname LIKE \'%«%\'
                 OR firstname LIKE \'%«%\'
                 OR firstname LIKE \'%===%\'
                 OR lastname LIKE \'%джиперы%\'
                 OR firstname LIKE \'%сервис%\'
                 OR lastname LIKE \'%сервис%\'
                 OR firstname LIKE \'%rem%\'
                 OR lastname LIKE \'%rem%\'
                 OR lastname LIKE \'%fiat%\'
                 OR lastname LIKE \'%Востокстойгрупп%\'
                 OR lastname LIKE \'%НП ЦУД ПО ДИС%\'
                 OR lastname LIKE \'%представительство%\')
                AND firstname <> \'===Бахтиор\'
            )
        ');

        $this->addSql('DELETE person FROM person WHERE person.id IN (SELECT temp.id FROM temp)');

        $this->addSql('INSERT INTO organization (id, name, email, telephone, office_phone)
            SELECT id, TRIM(CONCAT_WS(\' \', firstname, lastname)), email, telephone, office_phone FROM temp');

        $this->addSql('UPDATE operand SET type = 2 WHERE id IN (SELECT id FROM temp)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE organization DROP office_phone');
    }
}
