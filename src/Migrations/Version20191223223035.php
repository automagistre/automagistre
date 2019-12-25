<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191223223035 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'Landlord only');

        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69DF64382E3');
        $this->addSql('DROP INDEX IDX_773DE69DF64382E3 ON car');
        $this->addSql('ALTER TABLE car CHANGE car_model_id model_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D7975B7E7 FOREIGN KEY (model_id) REFERENCES car_model (id)');
        $this->addSql('CREATE INDEX IDX_773DE69D7975B7E7 ON car (model_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'landlord'), 'Landlord only');

        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D7975B7E7');
        $this->addSql('DROP INDEX IDX_773DE69D7975B7E7 ON car');
        $this->addSql('ALTER TABLE car CHANGE model_id car_model_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69DF64382E3 FOREIGN KEY (car_model_id) REFERENCES car_model (id)');
        $this->addSql('CREATE INDEX IDX_773DE69DF64382E3 ON car (car_model_id)');
    }
}
