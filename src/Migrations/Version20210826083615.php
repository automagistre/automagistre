<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;
use function implode;
use function sprintf;

final class Version20210826083615 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE income_accrue (
          id UUID NOT NULL,
          income_id UUID DEFAULT NULL,
          tenant_id SMALLINT DEFAULT NULL,
          amount_amount BIGINT DEFAULT NULL,
          amount_currency_code VARCHAR(3) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_425DFA41640ED2C0 ON income_accrue (income_id)');
        $this->addSql('COMMENT ON COLUMN income_accrue.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN income_accrue.income_id IS \'(DC2Type:income_id)\'');
        $this->addSql('COMMENT ON COLUMN income_accrue.tenant_id IS \'(DC2Type:tenant_enum)\'');
        $this->addSql('ALTER TABLE
          income_accrue
        ADD
          CONSTRAINT FK_425DFA41640ED2C0 FOREIGN KEY (income_id) REFERENCES income (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE income ADD accrue_id UUID DEFAULT NULL');
        $incomes = $this->connection->fetchAllAssociativeIndexed('SELECT id FROM income ORDER BY id');

        $values = [];
        foreach ($incomes as $incomeId => $_) {
            $values[] = sprintf('(\'%s\', \'%s\')', $incomeId, Uuid::uuid6()->toString());
        }

        if ([] !== $values) {
            $values = implode(', ', $values);

            $this->addSql("
            UPDATE income SET accrue_id = v.accrueId::uuid
            FROM (VALUES {$values}) AS v(incomeId, accrueId)
            WHERE income.id = v.incomeId::uuid
            ");
        }

        $this->addSql('
            INSERT INTO income_accrue (id, income_id, tenant_id, amount_amount, amount_currency_code)
            SELECT accrue_id, id, tenant_id, accrued_amount_amount, accrued_amount_currency_code
            FROM income
            WHERE accrued_at IS NOT NULL
            ');

        $this->addSql('
            INSERT INTO created_by (id, user_id, created_at, tenant_id)
            SELECT accrue_id, accrued_by_id, accrued_at, tenant_id
            FROM income
            WHERE accrued_at IS NOT NULL
            ');

        $this->addSql('ALTER TABLE income DROP CONSTRAINT fk_3fa862d0748c73b5');
        $this->addSql('DROP INDEX idx_3fa862d0748c73b5');
        $this->addSql('DROP INDEX idx_3fa862d06fc55e56');
        $this->addSql('ALTER TABLE income DROP accrue_id');
        $this->addSql('ALTER TABLE income DROP accrued_by_id');
        $this->addSql('ALTER TABLE income DROP accrued_at');
        $this->addSql('ALTER TABLE income DROP accrued_amount_amount');
        $this->addSql('ALTER TABLE income DROP accrued_amount_currency_code');

        $this->addSql('UPDATE created_by SET user_id = \'4f476e3b-dcdb-4b32-8282-3c6833ba550c\' WHERE user_id =\'60fcb1e6-1240-4bbc-938a-b107823eb47f\'');
        $this->addSql('UPDATE created_by SET user_id = \'57ec2612-46e8-46d7-aa82-34c49730bdf0\' WHERE user_id =\'3ea34a85-1cd0-465d-9dae-f552b6199235\'');
        $this->addSql('UPDATE created_by SET user_id = \'89786b82-0337-48e4-a659-414581c0c2e6\' WHERE user_id =\'eba055ca-963c-4e0d-8b5d-e52e06cc2254\'');
        $this->addSql('UPDATE created_by SET user_id = \'973b18d2-d919-424c-9e42-632fae1fe717\' WHERE user_id =\'1eaa7365-c86f-6742-adad-02420a0005d6\'');
        $this->addSql('UPDATE created_by SET user_id = \'3288c27c-b22c-460e-baf8-d637ff45af7d\' WHERE user_id =\'1ebf0b9d-e019-6eda-ad18-02420a000a93\'');
        $this->addSql('UPDATE created_by SET user_id = \'500fb136-3881-4faa-9cfc-416ded4183f6\' WHERE user_id =\'1eaa6a4e-05d2-6240-ad67-02420a000587\'');
        $this->addSql('UPDATE created_by SET user_id = \'bcb2e170-c2db-413e-8dbd-c3a9b485cb19\' WHERE user_id =\'0b21cae0-358e-4d6a-a182-841d06e59647\'');
        $this->addSql('UPDATE created_by SET user_id = \'1d092a29-1407-4061-a5f2-ae93b474c158\' WHERE user_id =\'82e0e54c-0d11-4043-abd3-8b19eb43b400\'');
        $this->addSql('UPDATE created_by SET user_id = \'53e1898d-3f6b-4d21-b895-e6091e8615f2\' WHERE user_id =\'13efe794-e400-4270-93df-4c54b4ecc932\'');
        $this->addSql('UPDATE created_by SET user_id = \'aa90358e-70aa-4e8c-8409-ffc5f1683377\' WHERE user_id =\'4ffc24e2-8e60-42e0-9c8f-7a73888b2da6\'');
        $this->addSql('UPDATE created_by SET user_id = \'b5f10063-5172-4acb-857c-9d9fdd2c2e87\' WHERE user_id =\'123ff639-a882-417e-a01f-b65864dc9f52\'');
        $this->addSql('UPDATE created_by SET user_id = \'835cfd16-c90c-45f2-8c87-546d1906baaf\' WHERE user_id =\'637a2ff6-a085-428c-87dc-0046abe11cc9\'');
        $this->addSql('UPDATE created_by SET user_id = \'28e30a2a-dfa5-4b6d-91f1-f32d6bbd96e5\' WHERE user_id =\'59861141-83b2-416c-b672-8ba8a1cb76b2\'');
        $this->addSql('UPDATE created_by SET user_id = \'d07b27d8-98d0-4845-ba1e-bad3863fe49b\' WHERE user_id =\'8614503b-ca5f-4b3a-8142-dadb011462a0\'');
        $this->addSql('UPDATE created_by SET user_id = \'410b4417-58a0-49ae-b6ac-39032475a1a3\' WHERE user_id =\'0ed195e6-b46c-4f95-b581-6d5a2e7ebf46\'');
        $this->addSql('UPDATE created_by SET user_id = \'c6a64068-dcb2-4e70-a144-147f7abda499\' WHERE user_id =\'1eab64c5-18b0-646c-9ac3-0242c0a8100a\'');
        $this->addSql('UPDATE created_by SET user_id = \'0dd7f47b-b841-4f2c-991a-ebef41d475d0\' WHERE user_id =\'5fe0541f-e192-4ce7-8d87-6bca1d557477\'');
        $this->addSql('UPDATE created_by SET user_id = \'b77c6dbc-883d-45ef-8ce4-f62a85e4610b\' WHERE user_id =\'642a07d8-db67-48c5-945d-54d784c3ac28\'');
        $this->addSql('UPDATE created_by SET user_id = \'6573cd4f-1e67-40a0-b14e-d435d12f72ca\' WHERE user_id =\'62398613-c0e0-4291-8613-d6c0cc2ba4b2\'');
        $this->addSql('UPDATE created_by SET user_id = \'76128bb3-a202-4e05-a669-5612a37fb090\' WHERE user_id =\'80b9dc68-c1ce-4222-9b7c-c8fa0d2ed59c\'');

        $this->addSql('DELETE FROM migration_versions WHERE version = \'App\\Migrations\\Version20210824091039\'');
    }
}
