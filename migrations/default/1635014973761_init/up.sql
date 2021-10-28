--- Hasura
CREATE OR REPLACE FUNCTION public.set_current_timestamp_updated_at() RETURNS TRIGGER AS
$$
DECLARE
    _new record;
BEGIN
    _new := new;
    _new."updated_at" = NOW();
    RETURN _new;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.hasura_timestampable(target_table regclass) RETURNS void AS
$$
DECLARE
    trigger_name text;
BEGIN
    trigger_name = 'set_' || REPLACE(target_table::text, '.', '_') || '_updated_at';

    EXECUTE 'ALTER TABLE ' || target_table || ' ADD COLUMN "created_at" timestamptz NOT NULL DEFAULT NOW()';
    EXECUTE 'ALTER TABLE ' || target_table || ' ADD COLUMN "updated_at" timestamptz NOT NULL DEFAULT NOW()';

    EXECUTE 'CREATE TRIGGER "' || trigger_name || '" BEFORE UPDATE ON ' || target_table ||
            ' FOR EACH ROW EXECUTE PROCEDURE public.set_current_timestamp_updated_at()';
    EXECUTE 'COMMENT ON TRIGGER ' || trigger_name || ' ON ' || target_table ||
            ' IS ''trigger to set value of column "updated_at" to current timestamp on row update''';

    EXECUTE 'UPDATE ' || target_table ||
            ' t SET created_at = cb.created_at, updated_at = cb.created_at FROM public.created_by cb WHERE cb.id = t.id';
END ;
$$ LANGUAGE plpgsql;

--- Drop views

DROP VIEW public.appeal_view;
DROP VIEW public.calendar_entry_view;
DROP VIEW public.customer_transaction_view;
DROP VIEW public.customer_view;
DROP VIEW public.inventorization_part_view;
DROP VIEW public.inventorization_view;
DROP VIEW public.note_view;
DROP VIEW public.organization_view;
DROP VIEW public.part_analog_view;
DROP VIEW public.part_view;
DROP VIEW public.person_view;
DROP VIEW public.publish_view;
DROP VIEW public.salary_view;
DROP VIEW public.storage_part_view;
DROP VIEW public.supply_view;
DROP VIEW public.wallet_transaction_view;
DROP VIEW public.wallet_view;
DROP VIEW public.warehouse_view;

-- Manufacturer

ALTER TABLE public.manufacturer
    ALTER COLUMN "id" SET DEFAULT gen_random_uuid();

ALTER TABLE public.manufacturer
    DROP COLUMN "logo" CASCADE;

SELECT public.hasura_timestampable('public.manufacturer');

--- Vehicle

ALTER TABLE public.vehicle_model
    RENAME TO "vehicle";

ALTER TABLE public.vehicle
    ADD CONSTRAINT "vehicle_manufacturer_id_fkey" FOREIGN KEY ("manufacturer_id") REFERENCES public.manufacturer ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.vehicle
    ALTER COLUMN "id" SET DEFAULT gen_random_uuid();

SELECT public.hasura_timestampable('public.vehicle');

--- Part

ALTER TABLE public.part
    ALTER COLUMN "id" SET DEFAULT gen_random_uuid();

COMMENT ON COLUMN public.part."number" IS NULL;

ALTER TABLE public.part
    ADD COLUMN "comment" text NULL;

ALTER TABLE public.part
    ADD CONSTRAINT "part_manufacturer_id_fkey" FOREIGN KEY ("manufacturer_id") REFERENCES public.manufacturer ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

COMMENT ON COLUMN public.part."unit" IS NULL;

CREATE TABLE public.unit
(
    "id"   text NOT NULL,
    "name" text NOT NULL,
    PRIMARY KEY ("id")
);

SELECT public.hasura_timestampable('public.part');

---

INSERT INTO public.unit("id", "name")
VALUES (E'thing', E'Штука'),
       (E'package', E'Упаковка'),
       (E'milliliter', E'Миллилитр'),
       (E'liter', E'Литр'),
       (E'gram', E'Грамм'),
       (E'kilogram', E'Килограмм'),
       (E'millimeter', E'Миллиметр'),
       (E'meter', E'Метр')
;

ALTER TABLE public.part
    ALTER COLUMN "unit" TYPE text USING CASE WHEN unit = 1 THEN 'thing'
                                             WHEN unit = 2 THEN 'package'
                                             WHEN unit = 3 THEN 'milliliter'
                                             WHEN unit = 4 THEN 'liter'
                                             WHEN unit = 5 THEN 'gram'
                                             WHEN unit = 6 THEN 'kilogram'
                                             WHEN unit = 7 THEN 'millimeter'
                                             WHEN unit = 8 THEN 'meter' END;

ALTER TABLE public.part
    ADD CONSTRAINT "part_unit_fkey" FOREIGN KEY ("unit") REFERENCES public.unit ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

--- Tenant

ALTER TABLE public.tenant
    ADD CONSTRAINT "tenant_group_id_fkey" FOREIGN KEY ("group_id") REFERENCES public.tenant_group ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.tenant
    ALTER COLUMN "id" SET DEFAULT gen_random_uuid();

ALTER TABLE public.tenant_group
    ALTER COLUMN "id" SET DEFAULT gen_random_uuid();

ALTER TABLE public.user_permission
    RENAME TO "tenant_permission";

SELECT public.hasura_timestampable('public.tenant');
SELECT public.hasura_timestampable('public.tenant_group');
SELECT public.hasura_timestampable('public.tenant_permission');

BEGIN TRANSACTION;
ALTER TABLE public.tenant_permission
    DROP CONSTRAINT "user_permission_pkey";

ALTER TABLE public.tenant_permission
    ADD CONSTRAINT "user_permission_pkey" PRIMARY KEY ("user_id", "tenant_id");
COMMIT TRANSACTION;

ALTER TABLE public.tenant_permission
    DROP COLUMN "id" CASCADE;

BEGIN TRANSACTION;
ALTER TABLE public.tenant
    DROP CONSTRAINT "tenant_pkey";

ALTER TABLE public.tenant
    ADD CONSTRAINT "tenant_pkey" PRIMARY KEY ("id");
COMMIT TRANSACTION;

ALTER TABLE public.tenant_permission
    ADD CONSTRAINT "tenant_permission_tenant_id_fkey" FOREIGN KEY ("tenant_id") REFERENCES public.tenant ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.tenant
    RENAME COLUMN "display_name" TO "name";

ALTER TABLE public.tenant_group
    ADD COLUMN "name" text NOT NULL DEFAULT '';

---

UPDATE public.tenant_group
SET name = CASE WHEN identifier = 'demo' THEN 'Демо'
                WHEN identifier = 'automagistre' THEN 'Автомагистр'
                WHEN identifier = 'shavlev' THEN 'Щавлев В.А.' END;

--- Wallet

ALTER TABLE public.wallet
    ALTER COLUMN id SET DEFAULT gen_random_uuid();
ALTER TABLE public.wallet_transaction
    ALTER COLUMN id SET DEFAULT gen_random_uuid();

CREATE TABLE public.wallet_transaction_source
(
    "id"   text NOT NULL,
    "name" text NOT NULL,
    PRIMARY KEY ("id")
);
INSERT INTO public.wallet_transaction_source("id", "name")
VALUES (E'legacy', E'Какие то старые проводки'),
       (E'order_prepay', E'Предоплата по заказу'),
       (E'order_debit', E'Начисление по заказу'),
       (E'payroll', E'Выдача зарплаты'),
       (E'income_payment', E'Оплата за поставку'),
       (E'expense', E'Списание по статье расходов'),
       (E'operand_manual', E'Ручная проводка клиента'),
       (E'initial', E'Начальный баланс')
;
ALTER TABLE public.wallet_transaction
    ALTER COLUMN source TYPE text USING CASE WHEN source = 0 THEN 'legacy'
                                             WHEN source = 1 THEN 'order_prepay'
                                             WHEN source = 2 THEN 'order_debit'
                                             WHEN source = 3 THEN 'payroll'
                                             WHEN source = 4 THEN 'income_payment'
                                             WHEN source = 5 THEN 'expense'
                                             WHEN source = 6 THEN 'operand_manual'
                                             WHEN source = 7 THEN 'initial' END;

ALTER TABLE "public"."wallet_transaction"
    ADD CONSTRAINT "wallet_transaction_wallet_id_fkey" FOREIGN KEY ("wallet_id") REFERENCES "public"."wallet" ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE "public"."wallet_transaction"
    ADD CONSTRAINT "wallet_transaction_tenant_id_fkey" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenant" ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE "public"."wallet"
    ADD CONSTRAINT "wallet_tenant_id_fkey" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenant" ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE "public"."wallet_transaction"
    ADD CONSTRAINT "wallet_transaction_source_fkey" FOREIGN KEY ("source") REFERENCES "public"."wallet_transaction_source" ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

--- Wallet Balance

ALTER TABLE public.wallet
    ADD COLUMN "balance" numeric(10, 2) DEFAULT 0;

ALTER TABLE public.wallet
    ADD COLUMN "balance_at" timestamptz DEFAULT NOW();

ALTER TABLE public.wallet_transaction
    RENAME amount_amount TO amount;
ALTER TABLE public.wallet_transaction
    RENAME amount_currency_code TO currency;

ALTER TABLE public.wallet_transaction
    ALTER COLUMN amount TYPE numeric(10, 2) USING amount / 100;

CREATE OR REPLACE FUNCTION app_wallet_balance_update(uuid) RETURNS void AS
$$
BEGIN
    UPDATE wallet
    SET balance    = ( SELECT SUM(amount) FROM public.wallet_transaction WHERE wallet_id = $1 ),
        balance_at = NOW()
    WHERE id = $1;
END;
$$ LANGUAGE plpgsql;
CREATE OR REPLACE FUNCTION app_wallet_balance_update_trigger_procedure() RETURNS trigger AS
$$
BEGIN
    IF new.wallet_id <> old.wallet_id THEN CALL app_wallet_balance_update(old.wallet_id); END IF;

    CALL app_wallet_balance_update(new.wallet_id);

    RETURN new;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER app_wallet_balance_update_trigger
    AFTER INSERT OR DELETE OR UPDATE OF amount,wallet_id
    ON public.wallet_transaction
    FOR EACH ROW
EXECUTE PROCEDURE app_wallet_balance_update_trigger_procedure();
SELECT app_wallet_balance_update(id)
FROM wallet;

SELECT public.hasura_timestampable('public.wallet');
SELECT public.hasura_timestampable('public.wallet_transaction');

--- Audit

SELECT audit.audit_table('public.manufacturer');
SELECT audit.audit_table('public.vehicle');
SELECT audit.audit_table('public.part');
SELECT audit.audit_table('public.unit');
SELECT audit.audit_table('public.tenant');
SELECT audit.audit_table('public.tenant_group');
SELECT audit.audit_table('public.tenant_permission');
SELECT audit.audit_table('public.wallet');
SELECT audit.audit_table('public.wallet_transaction');
