<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210820183541 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_773de69d772e836a');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69D772E836A9033212A ON car (identifier, tenant_id)');
        $this->addSql('DROP INDEX uniq_e52ffdee96901f54');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE96901F549033212A ON orders (number, tenant_id)');
        $this->addSql('DROP INDEX uniq_1483a5e9f85e0677');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E06779033212A ON users (username, tenant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_1483A5E9F85E06779033212A');
        $this->addSql('CREATE UNIQUE INDEX uniq_1483a5e9f85e0677 ON users (username)');
        $this->addSql('DROP INDEX UNIQ_773DE69D772E836A9033212A');
        $this->addSql('CREATE UNIQUE INDEX uniq_773de69d772e836a ON car (identifier)');
        $this->addSql('DROP INDEX UNIQ_E52FFDEE96901F549033212A');
        $this->addSql('CREATE UNIQUE INDEX uniq_e52ffdee96901f54 ON orders (number)');
    }
}
