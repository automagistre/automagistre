<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220203211254 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE public.tenant RENAME COLUMN display_name TO name');
        $this->addSql('ALTER TABLE public.tenant_group ADD COLUMN name text DEFAULT NULL');
        $this->addSql(
            "
            UPDATE public.tenant_group
                SET name = CASE WHEN identifier = 'demo' THEN 'Демо'
                       WHEN identifier = 'automagistre' THEN 'Автомагистр'
                       WHEN identifier = 'shavlev' THEN 'Щавлев В.А.' END
            WHERE TRUE
        ",
        );
        $this->addSql('ALTER TABLE public.tenant_group ALTER name SET NOT NULL');

        $this->addSql(
            '
            ALTER TABLE public.tenant
                ADD CONSTRAINT tenant_group_id_fkey
                    FOREIGN KEY (group_id) REFERENCES public.tenant_group (id) ON UPDATE RESTRICT ON DELETE RESTRICT
        ',
        );

        $this->addSql('ALTER TABLE public.user_permission RENAME TO tenant_permission');

        $this->addSql('CREATE UNIQUE INDEX temporal_unique_idx ON public.tenant (id);');
        $this->addSql(
            '
            ALTER TABLE public.tenant_permission
                ADD CONSTRAINT tenant_permission_tenant_id_fkey
                    FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT
        ',
        );

        $this->addSql(
            '
        CREATE TABLE public.tenant_group_permission
            (
                user_id uuid NOT NULL,
                tenant_group_id uuid
                    REFERENCES tenant_group (id) NOT NULL,
                PRIMARY KEY (user_id, tenant_group_id)
            )',
        );

        $this->addSql(
            'INSERT INTO public.tenant_group_permission (user_id, tenant_group_id)
                    SELECT DISTINCT tp.user_id, t.group_id
                      FROM tenant_permission tp
                               JOIN tenant t
                               ON t.id = tp.tenant_id
            ',
        );

        $this->addSql(
            <<<'SQL'
            CREATE FUNCTION public.set_current_timestamp_updated_at() RETURNS trigger AS
            $$
            DECLARE
                _new record;
            BEGIN
                _new := new;
                _new.updated_at = NOW();
                RETURN _new;
            END;
            $$ LANGUAGE plpgsql
            SQL,
        );

        $this->addSql(
            <<<'SQL'
            CREATE FUNCTION public.timestampable(target_table regclass) RETURNS void AS
            $$
            DECLARE
                trigger_name text;
            BEGIN
                trigger_name = 'set_' || REPLACE(target_table::text, '.', '_') || '_updated_at';

                EXECUTE 'ALTER TABLE ' || target_table || ' ADD COLUMN "created_at" timestamptz NOT NULL DEFAULT NOW()';
                EXECUTE 'ALTER TABLE ' || target_table || ' ADD COLUMN "updated_at" timestamptz NOT NULL DEFAULT NOW()';

                IF (SELECT EXISTS(SELECT 1
                                    FROM information_schema.columns
                                   WHERE table_schema = 'public'
                                     AND table_name = target_table::text
                                     AND column_name = 'id'
                                     AND data_type = 'uuid')) THEN

                    EXECUTE 'UPDATE ' || target_table ||
                            ' t SET created_at = cb.created_at, updated_at = cb.created_at FROM public.created_by cb WHERE cb.id = t.id';
                END IF;

                EXECUTE 'CREATE TRIGGER "' || trigger_name || '" BEFORE UPDATE ON ' || target_table ||
                        ' FOR EACH ROW EXECUTE PROCEDURE public.set_current_timestamp_updated_at()';
                EXECUTE 'COMMENT ON TRIGGER ' || trigger_name || ' ON ' || target_table ||
                        ' IS ''trigger to set value of column "updated_at" to current timestamp on row update''';
            END ;
            $$ LANGUAGE plpgsql;
        SQL,
        );

        $this->addSql("SELECT public.timestampable('public.manufacturer')");
        $this->addSql("SELECT public.timestampable('public.tenant')");
        $this->addSql("SELECT public.timestampable('public.tenant_group')");
        $this->addSql("SELECT public.timestampable('public.tenant_permission')");
        $this->addSql("SELECT public.timestampable('public.tenant_group_permission')");

        $this->addSql('ALTER TABLE sms ALTER date_send DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
    }
}
