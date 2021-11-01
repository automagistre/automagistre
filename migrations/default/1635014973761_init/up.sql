--- Hasura
CREATE OR REPLACE FUNCTION public.set_current_timestamp_updated_at() RETURNS trigger AS
$$
DECLARE
    _new record;
BEGIN
    _new := new;
    _new.updated_at = NOW();
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
    ALTER COLUMN id SET DEFAULT gen_random_uuid();

ALTER TABLE public.manufacturer
    DROP COLUMN logo CASCADE;

SELECT public.hasura_timestampable('public.manufacturer');

--- Vehicle

ALTER TABLE public.vehicle_model
    RENAME TO vehicle;

ALTER TABLE public.vehicle
    ADD CONSTRAINT vehicle_manufacturer_id_fkey
        FOREIGN KEY (manufacturer_id) REFERENCES public.manufacturer (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.vehicle
    ALTER COLUMN id SET DEFAULT gen_random_uuid();

SELECT public.hasura_timestampable('public.vehicle');

--- Part

ALTER TABLE public.part
    ALTER COLUMN id SET DEFAULT gen_random_uuid();

COMMENT ON COLUMN public.part.number IS NULL;

ALTER TABLE public.part
    ADD COLUMN comment text NULL;

ALTER TABLE public.part
    ADD CONSTRAINT part_manufacturer_id_fkey
        FOREIGN KEY (manufacturer_id) REFERENCES public.manufacturer (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

COMMENT ON COLUMN public.part.unit IS NULL;

CREATE TABLE public.unit
    (
        id text NOT NULL,
        name text NOT NULL,
        PRIMARY KEY (id)
    );

SELECT public.hasura_timestampable('public.part');

---

INSERT INTO public.unit(id, name)
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
    ALTER COLUMN unit TYPE text USING CASE WHEN unit = 1 THEN 'thing'
                                           WHEN unit = 2 THEN 'package'
                                           WHEN unit = 3 THEN 'milliliter'
                                           WHEN unit = 4 THEN 'liter'
                                           WHEN unit = 5 THEN 'gram'
                                           WHEN unit = 6 THEN 'kilogram'
                                           WHEN unit = 7 THEN 'millimeter'
                                           WHEN unit = 8 THEN 'meter' END;

ALTER TABLE public.part
    ADD CONSTRAINT part_unit_fkey
        FOREIGN KEY (unit) REFERENCES public.unit (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

--- Tenant

ALTER TABLE public.tenant
    ADD CONSTRAINT tenant_group_id_fkey
        FOREIGN KEY (group_id) REFERENCES public.tenant_group (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.tenant
    ALTER COLUMN id SET DEFAULT gen_random_uuid();

ALTER TABLE public.tenant_group
    ALTER COLUMN id SET DEFAULT gen_random_uuid();

ALTER TABLE public.user_permission
    RENAME TO tenant_permission;

SELECT public.hasura_timestampable('public.tenant');
SELECT public.hasura_timestampable('public.tenant_group');
SELECT public.hasura_timestampable('public.tenant_permission');

BEGIN TRANSACTION;
ALTER TABLE public.tenant_permission
    DROP CONSTRAINT user_permission_pkey;

ALTER TABLE public.tenant_permission
    ADD CONSTRAINT user_permission_pkey
        PRIMARY KEY (user_id, tenant_id);
COMMIT TRANSACTION;

ALTER TABLE public.tenant_permission
    DROP COLUMN id CASCADE;

BEGIN TRANSACTION;
ALTER TABLE public.tenant
    DROP CONSTRAINT tenant_pkey;

ALTER TABLE public.tenant
    ADD CONSTRAINT tenant_pkey
        PRIMARY KEY (id);
COMMIT TRANSACTION;

ALTER TABLE public.tenant_permission
    ADD CONSTRAINT tenant_permission_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.tenant
    RENAME COLUMN display_name TO name;

ALTER TABLE public.tenant_group
    ADD COLUMN name text NOT NULL DEFAULT '';

CREATE TABLE public.tenant_group_permission
    (
        user_id uuid NOT NULL,
        tenant_group_id uuid
            REFERENCES tenant_group (id) NOT NULL,
        PRIMARY KEY (user_id, tenant_group_id)
    );

ALTER TABLE public.tenant_group_permission ADD id uuid DEFAULT NULL;
SELECT public.hasura_timestampable('tenant_group_permission');
ALTER TABLE public.tenant_group_permission DROP COLUMN id;

---

UPDATE public.tenant_group
   SET name = CASE WHEN identifier = 'demo' THEN 'Демо'
                   WHEN identifier = 'automagistre' THEN 'Автомагистр'
                   WHEN identifier = 'shavlev' THEN 'Щавлев В.А.' END;

INSERT INTO public.tenant_group_permission (user_id, tenant_group_id)
SELECT DISTINCT tp.user_id, t.group_id
  FROM tenant_permission tp
           JOIN tenant t
           ON t.id = tp.tenant_id;

--- Wallet

ALTER TABLE public.wallet
    ALTER COLUMN id SET DEFAULT gen_random_uuid();
ALTER TABLE public.wallet_transaction
    ALTER COLUMN id SET DEFAULT gen_random_uuid();

CREATE TABLE public.wallet_transaction_source
    (
        id text NOT NULL,
        name text NOT NULL,
        PRIMARY KEY (id)
    );
INSERT INTO public.wallet_transaction_source(id, name)
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

ALTER TABLE public.wallet_transaction
    ADD CONSTRAINT wallet_transaction_wallet_id_fkey
        FOREIGN KEY (wallet_id) REFERENCES public.wallet (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.wallet_transaction
    ADD CONSTRAINT wallet_transaction_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.wallet
    ADD CONSTRAINT wallet_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.wallet_transaction
    ADD CONSTRAINT wallet_transaction_source_fkey
        FOREIGN KEY (source) REFERENCES public.wallet_transaction_source (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

--- Wallet Balance

ALTER TABLE public.wallet
    ADD COLUMN balance numeric(12, 2) DEFAULT 0;

ALTER TABLE public.wallet
    ADD COLUMN balance_at timestamptz DEFAULT NOW();

ALTER TABLE public.wallet_transaction
    RENAME amount_amount TO amount;
ALTER TABLE public.wallet_transaction
    RENAME amount_currency_code TO currency;

ALTER TABLE public.wallet_transaction
    ALTER COLUMN amount TYPE numeric(12, 2) USING amount / 100;

CREATE OR REPLACE FUNCTION app_wallet_balance_update(uuid) RETURNS void AS
$$
BEGIN
    UPDATE wallet
       SET balance = (SELECT SUM(amount) FROM public.wallet_transaction WHERE wallet_id = $1),
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

--- Expense

ALTER TABLE public.expense
    ALTER COLUMN id SET DEFAULT gen_random_uuid();

SELECT public.hasura_timestampable('public.expense');

--- Warehouse

ALTER TABLE public.warehouse
    ALTER COLUMN id SET DEFAULT gen_random_uuid();
ALTER TABLE public.warehouse
    ADD COLUMN name text DEFAULT NULL;
ALTER TABLE public.warehouse
    ADD COLUMN code text DEFAULT NULL;
ALTER TABLE public.warehouse
    ADD COLUMN parent_id uuid DEFAULT NULL;
UPDATE public.warehouse t
   SET name = (SELECT sub.name FROM warehouse_name sub WHERE sub.warehouse_id = t.id ORDER BY sub.id DESC LIMIT 1);
UPDATE public.warehouse t
   SET code = (SELECT sub.code FROM warehouse_code sub WHERE sub.warehouse_id = t.id ORDER BY sub.id DESC LIMIT 1);
UPDATE public.warehouse t
   SET parent_id = (SELECT sub.warehouse_parent_id
                      FROM warehouse_parent sub
                     WHERE sub.warehouse_id = t.id
                     ORDER BY sub.id DESC
                     LIMIT 1);

DROP TABLE warehouse_name;
DROP TABLE warehouse_code;
DROP TABLE warehouse_parent;

ALTER TABLE public.warehouse
    ADD CONSTRAINT warehouse_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

SELECT public.hasura_timestampable('public.warehouse');

--- Part Case

ALTER TABLE public.part_case
    ALTER COLUMN id SET DEFAULT gen_random_uuid();

ALTER TABLE public.part_case
    ADD CONSTRAINT part_case_part_id_fkey
        FOREIGN KEY (part_id) REFERENCES public.part (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.part_case
    ADD CONSTRAINT part_case_vehicle_id_fkey
        FOREIGN KEY (vehicle_id) REFERENCES public.vehicle (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

SELECT public.hasura_timestampable('public.part_case');

--- Customer

CREATE TABLE public.contact_type
    (
        id text
            PRIMARY KEY NOT NULL,
        name text NOT NULL
    );
INSERT INTO public.contact_type(id, name)
VALUES (E'LLC', E'ООО'),
       (E'SP', E'ИП'),
       (E'CJSC', E'ЗАО'),
       (E'JSC', E'ОАО'),
       (E'UNKNOWN', E'Неизвестно'),
       (E'NP', E'ФЛ') -- Natural Person
;

DROP TABLE IF EXISTS public.contact;
CREATE TABLE public.contact
    (
        id uuid
            PRIMARY KEY DEFAULT gen_random_uuid(),
        type text
            REFERENCES public.contact_type (id) DEFAULT NULL,
        name jsonb DEFAULT '{}'::jsonb,
        telephone text DEFAULT NULL,
        email text DEFAULT NULL,
        contractor boolean DEFAULT FALSE,
        supplier boolean DEFAULT FALSE,
        requisites jsonb DEFAULT '{}'::jsonb,
        tenant_group_id uuid NOT NULL,
        FOREIGN KEY (tenant_group_id) REFERENCES tenant_group (id)
    );
CREATE TABLE public.contact_reference_type
    (
        id text
            PRIMARY KEY NOT NULL,
        name text NOT NULL
    );
DROP TABLE IF EXISTS public.contact_reference;
CREATE TABLE public.contact_reference
    (
        from_id uuid
            REFERENCES public.contact (id) NOT NULL,
        to_id uuid
            REFERENCES public.contact (id) NOT NULL,
        type text
            REFERENCES public.contact_reference_type (id) DEFAULT NULL,
        comment text DEFAULT NULL,
        tenant_group_id uuid NOT NULL,
        FOREIGN KEY (tenant_group_id) REFERENCES tenant_group (id),
        PRIMARY KEY (from_id, to_id)
    );

INSERT INTO public.contact (id, type, name, telephone, email, contractor, supplier, tenant_group_id)
SELECT id, 'NP', JSON_BUILD_OBJECT('firstname', firstname, 'lastname', lastname, 'middlename', NULL), telephone, email,
       contractor, seller, tenant_group_id
  FROM person;

UPDATE organization
   SET name = REPLACE(name, '.', ' ')
 WHERE name LIKE 'ИП%';

INSERT INTO public.contact (id, type, name, telephone, email, contractor, supplier, tenant_group_id, requisites)
SELECT id,
       CASE WHEN name LIKE 'ООО%' THEN 'LLC'
            WHEN name LIKE 'ИП%' THEN 'SP'
            WHEN name LIKE 'ЗАО%' THEN 'CJSC'
            WHEN name LIKE 'ОАО%' THEN 'JSC'
            ELSE 'UNKNOWN' END,
       CASE WHEN name LIKE 'ИП%' THEN JSON_BUILD_OBJECT('firstname', INITCAP((STRING_TO_ARRAY(name, ' '))[3]),
                                                        'lastname', INITCAP((STRING_TO_ARRAY(name, ' '))[2]),
                                                        'middlename', INITCAP((STRING_TO_ARRAY(name, ' '))[4]))
            ELSE JSON_BUILD_OBJECT('name', name, 'full_name', NULL) END, telephone, email, contractor, seller,
       tenant_group_id,
       JSON_BUILD_OBJECT('bank', requisite_bank, 'legal_address', requisite_legal_address, 'address', address, 'ogrn',
                         requisite_ogrn, 'inn', requisite_inn, 'kpp', requisite_kpp, 'rs', requisite_rs, 'ks',
                         requisite_ks, 'bik', requisite_bik)
  FROM organization;

INSERT INTO public.contact_reference (from_id, to_id, tenant_group_id)
SELECT DISTINCT p.id, o.id, o.tenant_group_id
  FROM public.person p
           JOIN public.organization o
           ON o.telephone = p.telephone AND o.tenant_group_id = p.tenant_group_id;

UPDATE contact
   SET telephone = NULL
 WHERE id IN (SELECT DISTINCT o.id
                FROM public.person p
                         JOIN public.organization o
                         ON o.telephone = p.telephone);

INSERT INTO contact (id, telephone, tenant_group_id)
VALUES ('c2bebe04-9dd6-4f2b-9d32-e069451073bd',
        (SELECT telephone FROM contact WHERE id = '1ea87f90-c050-60fe-9ffc-02420a000547' LIMIT 1),
        '1ec13d33-3f41-6cf0-b012-02420a000f18');
INSERT INTO contact_reference (from_id, to_id, tenant_group_id)
VALUES ('c2bebe04-9dd6-4f2b-9d32-e069451073bd', '1ea87f90-c050-60fe-9ffc-02420a000547',
        '1ec13d33-3f41-6cf0-b012-02420a000f18');
INSERT INTO contact_reference (from_id, to_id, tenant_group_id)
VALUES ('c2bebe04-9dd6-4f2b-9d32-e069451073bd', '1ea87f90-c063-60d2-9183-02420a000547',
        '1ec13d33-3f41-6cf0-b012-02420a000f18');

INSERT INTO contact (id, telephone, tenant_group_id)
VALUES ('f5a34863-9036-40de-a639-e2afc06e2962',
        (SELECT telephone FROM contact WHERE id = '1ea87f90-c06c-679a-99a2-02420a000547' LIMIT 1),
        '1ec13d33-3f41-6cf0-b012-02420a000f18');
INSERT INTO contact_reference (from_id, to_id, tenant_group_id)
VALUES ('f5a34863-9036-40de-a639-e2afc06e2962', '1ea87f90-c06c-679a-99a2-02420a000547',
        '1ec13d33-3f41-6cf0-b012-02420a000f18');
INSERT INTO contact_reference (from_id, to_id, tenant_group_id)
VALUES ('f5a34863-9036-40de-a639-e2afc06e2962', '1eb64ae7-f469-6572-9559-0242ac120038',
        '1ec13d33-3f41-6cf0-b012-02420a000f18');

UPDATE contact
   SET telephone = NULL
 WHERE id IN ('1ea87f90-c050-60fe-9ffc-02420a000547', '1ea87f90-c063-60d2-9183-02420a000547',
              '1ea87f90-c06c-679a-99a2-02420a000547', '1eb64ae7-f469-6572-9559-0242ac120038');

CREATE UNIQUE INDEX contact_unique_phone_idx ON public.contact (telephone, tenant_group_id);

-- DROP TABLE person;
-- DROP TABLE organization;

SELECT public.hasura_timestampable('contact');
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
SELECT audit.audit_table('public.expense');
SELECT audit.audit_table('public.warehouse');
SELECT audit.audit_table('public.part_case');
SELECT audit.audit_table('public.contact');
