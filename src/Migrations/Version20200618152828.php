<?php

declare(strict_types=1);

namespace App\Migrations;

use function array_map;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function implode;
use Ramsey\Uuid\Uuid;
use function sprintf;

final class Version20200618152828 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE operand_transaction ADD uuid UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN operand_transaction.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE wallet_transaction ADD uuid UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.uuid IS \'(DC2Type:uuid)\'');

        $values = $this->connection->fetchAll('SELECT id FROM operand_transaction');
        $values = array_map(fn (array $row) => sprintf('(\'%s\', %s)', Uuid::uuid6()->toString(), $row['id']), $values);

        if ([] !== $values) {
            $values = implode(',', $values);

            $this->addSql("
                UPDATE operand_transaction AS t SET
                uuid = v.uuid::uuid
                FROM (VALUES {$values}) AS v(uuid, id) 
                WHERE v.id = t.id;
            ");
        }

        $values = $this->connection->fetchAll('SELECT id FROM wallet_transaction');
        $values = array_map(fn (array $row) => sprintf('(\'%s\', %s)', Uuid::uuid6()->toString(), $row['id']), $values);

        if ([] !== $values) {
            $values = implode(',', $values);

            $this->addSql("
                UPDATE wallet_transaction AS t SET
                uuid = v.uuid::uuid
                FROM (VALUES {$values}) AS v(uuid, id) 
                WHERE v.id = t.id;
            ");
        }

        $this->addSql('ALTER TABLE operand_transaction ALTER uuid SET NOT NULL');
        $this->addSql('ALTER TABLE wallet_transaction ALTER uuid SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE operand_transaction DROP uuid');
        $this->addSql('ALTER TABLE wallet_transaction DROP uuid');
    }
}
