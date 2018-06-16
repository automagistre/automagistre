<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170422194703 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('INSERT INTO operand (type) VALUES (2)');
        $this->addSql('INSERT INTO organization (id, name, address, telephone) VALUES (LAST_INSERT_ID(), \'ООО ИКСОРА\', \'603093, г.Нижний Новгород, ул. Деловая, 7\', \'8 (831) 461-78-71\')');
        $this->addSql('INSERT INTO partner_operand (operand_id, name) VALUES (LAST_INSERT_ID(), \'ixora\')');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
