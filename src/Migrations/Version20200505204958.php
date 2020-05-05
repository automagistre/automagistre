<?php

declare(strict_types=1);

namespace App\Migrations;

use function array_map;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function implode;
use LogicException;
use Ramsey\Uuid\Uuid;
use function sprintf;
use function strpos;

final class Version20200505204958 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE income ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE income_part ADD income_uuid UUID DEFAULT NULL');

        $conn = $this->connection;

        $stmt = $conn->executeQuery('SELECT id from income ORDER BY id');
        $values = $stmt->fetchAll();
        if ([] !== $values) {
            $values = array_map(fn (array $row) => sprintf("(%s, '%s'::uuid)", $row['id'], Uuid::uuid6()->toString()), $values);
            $values = implode(', ', $values);
            $this->addSql("UPDATE income as t SET uuid = v.uuid FROM (values {$values}) AS v(id, uuid) WHERE t.id = v.id");
        }

        $this->addSql('UPDATE income_part SET income_uuid = (SELECT uuid FROM income WHERE income_part.income_id = income.id)');

        $this->addSql('ALTER TABLE income_part DROP CONSTRAINT fk_834566e8640ed2c0');
        $this->addSql('DROP INDEX idx_834566e8640ed2c0');
        $this->addSql('ALTER TABLE income DROP id');
        $this->addSql('ALTER TABLE income RENAME uuid TO id');
        $this->addSql('ALTER TABLE income ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE income_part DROP income_id');
        $this->addSql('ALTER TABLE income_part RENAME income_uuid TO income_id');

        $this->addSql('ALTER TABLE income ALTER id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN income.id IS \'(DC2Type:income_id)\'');

        $this->addSql('ALTER TABLE income_part ALTER income_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN income_part.income_id IS \'(DC2Type:income_id)\'');
        $this->addSql('ALTER TABLE 
          income_part 
        ADD 
          CONSTRAINT FK_834566E8640ED2C0 FOREIGN KEY (income_id) REFERENCES income (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_834566E8640ED2C0 ON income_part (income_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        throw new LogicException('Nope');
    }
}
