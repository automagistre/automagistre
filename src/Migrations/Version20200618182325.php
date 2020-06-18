<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;
use function sprintf;

final class Version20200618182325 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE wallet_id_seq CASCADE');
        $this->addSql('ALTER TABLE wallet ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE wallet_transaction ADD wallet_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE expense ADD wallet_uuid UUID DEFAULT NULL');

        // data migration
        $ids = $this->connection->fetchAll('SELECT id FROM wallet');
        foreach ($ids as ['id' => $id]) {
            $uuid = Uuid::uuid6()->toString();

            $this->addSql(sprintf('UPDATE wallet SET uuid = \'%s\'::uuid WHERE id = %s', $uuid, $id));
            $this->addSql(sprintf('UPDATE wallet_transaction SET wallet_id = \'%s\'::uuid WHERE recipient_id = %s', $uuid, $id));
            $this->addSql(sprintf('UPDATE expense SET wallet_uuid = \'%s\'::uuid WHERE wallet_id = %s', $uuid, $id));
        }
        // data migration

        $this->addSql('ALTER TABLE expense DROP wallet_id');
        $this->addSql('ALTER TABLE expense RENAME wallet_uuid TO wallet_id');
        $this->addSql('ALTER TABLE wallet_transaction DROP recipient_id');
        $this->addSql('ALTER TABLE wallet DROP id');
        $this->addSql('ALTER TABLE wallet RENAME uuid TO id');

        $this->addSql('COMMENT ON COLUMN expense.wallet_id IS \'(DC2Type:wallet_id)\'');
        $this->addSql('COMMENT ON COLUMN wallet.id IS \'(DC2Type:wallet_id)\'');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.wallet_id IS \'(DC2Type:wallet_id)\'');

        $this->addSql('ALTER TABLE wallet ALTER id SET NOT NULL;');
        $this->addSql('ALTER TABLE wallet ADD PRIMARY KEY (id);');
        $this->addSql('ALTER TABLE wallet_transaction ADD CONSTRAINT FK_7DAF972712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('CREATE INDEX IDX_7DAF972712520F3 ON wallet_transaction (wallet_id);');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('CREATE INDEX IDX_2D3A8DA6712520F3 ON expense (wallet_id);');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE wallet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE expense ALTER wallet_id TYPE INT');
        $this->addSql('ALTER TABLE expense ALTER wallet_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN expense.wallet_id IS NULL');
        $this->addSql('ALTER TABLE wallet_transaction ALTER recipient_id TYPE INT');
        $this->addSql('ALTER TABLE wallet_transaction ALTER recipient_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN wallet_transaction.recipient_id IS NULL');
        $this->addSql('ALTER TABLE wallet ALTER id TYPE INT');
        $this->addSql('ALTER TABLE wallet ALTER id DROP DEFAULT');
        $this->addSql('CREATE SEQUENCE wallet_id_seq');
        $this->addSql('SELECT setval(\'wallet_id_seq\', (SELECT MAX(id) FROM wallet))');
        $this->addSql('ALTER TABLE wallet ALTER id SET DEFAULT nextval(\'wallet_id_seq\')');
        $this->addSql('COMMENT ON COLUMN wallet.id IS NULL');
    }
}
