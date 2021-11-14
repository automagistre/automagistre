CREATE FUNCTION public.set_current_timestamp_updated_at() RETURNS trigger AS
$$
DECLARE
    _new record;
BEGIN
    _new := new;
    _new.updated_at = NOW();
    RETURN _new;
END;
$$ LANGUAGE plpgsql;

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

CREATE OR REPLACE FUNCTION public.set_balance(target_table regclass, target_uuid uuid) RETURNS void AS
$$
BEGIN
    EXECUTE 'UPDATE ' || target_table || '
       SET balance = (SELECT SUM(amount) FROM public.money_transfer WHERE target_id = ''' || target_uuid || '''::uuid),
           balance_at = NOW()
     WHERE id = ''' || target_uuid || '''::uuid';
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
DROP VIEW public.salary_view;
DROP VIEW public.storage_part_view;
DROP VIEW public.supply_view;
DROP VIEW public.wallet_transaction_view;
DROP VIEW public.wallet_view;
DROP VIEW public.warehouse_view;

-- Drop garbage

DROP TABLE cron_report;
DROP TABLE cron_job;
DROP SEQUENCE cron_job_id_seq;
DROP SEQUENCE cron_report_id_seq;
DROP TABLE migration_versions;

-- Manufacturer

ALTER TABLE public.manufacturer ALTER COLUMN id SET DEFAULT gen_random_uuid();

ALTER TABLE public.manufacturer
    DROP COLUMN logo CASCADE;

SELECT public.timestampable('public.manufacturer');

--- Part

ALTER TABLE public.part ALTER COLUMN id SET DEFAULT gen_random_uuid();

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

SELECT public.timestampable('public.part');

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

ALTER TABLE public.part ALTER COLUMN unit TYPE text USING CASE WHEN unit = 1 THEN 'thing'
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

ALTER TABLE public.tenant ALTER COLUMN id SET DEFAULT gen_random_uuid();

ALTER TABLE public.tenant_group ALTER COLUMN id SET DEFAULT gen_random_uuid();

ALTER TABLE public.user_permission RENAME TO tenant_permission;

SELECT public.timestampable('public.tenant');
SELECT public.timestampable('public.tenant_group');
SELECT public.timestampable('public.tenant_permission');

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

ALTER TABLE public.tenant RENAME COLUMN display_name TO name;

ALTER TABLE public.tenant_group ADD COLUMN name text NOT NULL DEFAULT '';

CREATE TABLE public.tenant_group_permission
    (
        user_id uuid NOT NULL,
        tenant_group_id uuid
            REFERENCES tenant_group (id) NOT NULL,
        PRIMARY KEY (user_id, tenant_group_id)
    );

ALTER TABLE public.tenant_group_permission ADD id uuid DEFAULT NULL;
SELECT public.timestampable('public.tenant_group_permission');
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


--- Contact

CREATE TABLE public.legal_form_type
    (
        id text
            PRIMARY KEY NOT NULL,
        name text NOT NULL
    );
INSERT INTO public.legal_form_type(id, name)
VALUES (E'person', 'Человек'),
       (E'organization', 'Организация')
;

CREATE TABLE public.legal_form
    (
        id text
            PRIMARY KEY NOT NULL,
        short_name text NOT NULL,
        full_name text NOT NULL,
        type text
            REFERENCES public.legal_form_type (id) ON UPDATE CASCADE NOT NULL
    );
INSERT INTO public.legal_form(id, short_name, full_name, type)
VALUES (E'LLC', 'ООО', 'Общество с ограниченной ответственностью', 'organization'),
       (E'SP', 'ИП', 'Индивидуальный предприниматель', 'person'),
       (E'CJSC', 'ЗАО', 'Закрытое акционерное общество', 'organization'),
       (E'JSC', 'ОАО', 'Открытое акционерное общество', 'organization'),
       (E'unknown', 'Неизвестно', 'Неизвестно', 'organization'),
       (E'NP', 'ФЛ', 'Физическое лицо', 'person'),
       (E'AO', 'АО', 'Акционерное общество', 'organization'),
       (E'PAO', 'ПАО', 'Публичное акционерное общество', 'organization'),
       (E'NPO', 'НКО', 'Некоммерческая организация', 'organization')
;
SELECT public.timestampable('public.legal_form');

CREATE TABLE public.contact
    (
        id uuid
            PRIMARY KEY DEFAULT gen_random_uuid(),
        legal_form text
            REFERENCES public.legal_form (id) ON UPDATE CASCADE DEFAULT 'unknown',
        name jsonb DEFAULT '{}'::jsonb,
        telephone text DEFAULT NULL,
        email text DEFAULT NULL,
        contractor boolean DEFAULT FALSE,
        supplier boolean DEFAULT FALSE,
        requisites jsonb DEFAULT '{}'::jsonb,
        tenant_group_id uuid NOT NULL,
        FOREIGN KEY (tenant_group_id) REFERENCES tenant_group (id)
    );

CREATE TABLE public.contact_relation
    (
        id uuid
            PRIMARY KEY DEFAULT gen_random_uuid(),
        source_id uuid
            REFERENCES public.contact (id) NOT NULL,
        target_id uuid
            REFERENCES public.contact (id) NOT NULL,
        comment text DEFAULT NULL,
        tenant_group_id uuid NOT NULL,
        FOREIGN KEY (tenant_group_id) REFERENCES tenant_group (id)
    );
SELECT public.timestampable('public.contact_relation');

INSERT INTO public.contact (id, legal_form, name, telephone, email, contractor, supplier, tenant_group_id)
SELECT id, 'NP', JSON_BUILD_OBJECT('firstname', firstname, 'lastname', lastname, 'middlename', NULL), telephone, email,
       contractor, seller, tenant_group_id
  FROM person;

UPDATE public.organization
   SET name = REPLACE(name, '.', ' ')
 WHERE name LIKE 'ИП%';

INSERT INTO public.contact (id, legal_form, name, telephone, email, contractor, supplier, tenant_group_id, requisites)
SELECT id,
       CASE WHEN name LIKE 'ООО%' THEN 'LLC'
            WHEN name LIKE 'ИП%' THEN 'SP'
            WHEN name LIKE 'ЗАО%' THEN 'CJSC'
            WHEN name LIKE 'ОАО%' THEN 'JSC'
            ELSE 'unknown' END,
       CASE WHEN name LIKE 'ИП%' THEN JSON_BUILD_OBJECT('firstname', INITCAP((STRING_TO_ARRAY(name, ' '))[3]),
                                                        'lastname', INITCAP((STRING_TO_ARRAY(name, ' '))[2]),
                                                        'middlename', INITCAP((STRING_TO_ARRAY(name, ' '))[4]))
            ELSE JSON_BUILD_OBJECT('name', name, 'full_name', NULL) END, telephone, email, contractor, seller,
       tenant_group_id,
       JSON_BUILD_OBJECT('bank', requisite_bank, 'legal_address', requisite_legal_address, 'address', address, 'ogrn',
                         requisite_ogrn, 'inn', requisite_inn, 'kpp', requisite_kpp, 'rs', requisite_rs, 'ks',
                         requisite_ks, 'bik', requisite_bik)
  FROM organization;

INSERT INTO public.contact_relation (source_id, target_id, tenant_group_id)
SELECT DISTINCT p.id, o.id, o.tenant_group_id
  FROM public.person p
           JOIN public.organization o
           ON o.telephone = p.telephone AND o.tenant_group_id = p.tenant_group_id;

UPDATE public.contact
   SET telephone = NULL
 WHERE id IN (SELECT DISTINCT o.id
                FROM public.person p
                         JOIN public.organization o
                         ON o.telephone = p.telephone);

INSERT INTO public.contact (id, telephone, tenant_group_id)
VALUES ('c2bebe04-9dd6-4f2b-9d32-e069451073bd',
        (SELECT telephone FROM contact WHERE id = '1ea87f90-c050-60fe-9ffc-02420a000547' LIMIT 1),
        '1ec13d33-3f41-6cf0-b012-02420a000f18');
INSERT INTO public.contact_relation (source_id, target_id, tenant_group_id)
VALUES ('c2bebe04-9dd6-4f2b-9d32-e069451073bd', '1ea87f90-c050-60fe-9ffc-02420a000547',
        '1ec13d33-3f41-6cf0-b012-02420a000f18');
INSERT INTO public.contact_relation (source_id, target_id, tenant_group_id)
VALUES ('c2bebe04-9dd6-4f2b-9d32-e069451073bd', '1ea87f90-c063-60d2-9183-02420a000547',
        '1ec13d33-3f41-6cf0-b012-02420a000f18');

INSERT INTO public.contact (id, telephone, tenant_group_id)
VALUES ('f5a34863-9036-40de-a639-e2afc06e2962',
        (SELECT telephone FROM contact WHERE id = '1ea87f90-c06c-679a-99a2-02420a000547' LIMIT 1),
        '1ec13d33-3f41-6cf0-b012-02420a000f18');
INSERT INTO public.contact_relation (source_id, target_id, tenant_group_id)
VALUES ('f5a34863-9036-40de-a639-e2afc06e2962', '1ea87f90-c06c-679a-99a2-02420a000547',
        '1ec13d33-3f41-6cf0-b012-02420a000f18');
INSERT INTO public.contact_relation (source_id, target_id, tenant_group_id)
VALUES ('f5a34863-9036-40de-a639-e2afc06e2962', '1eb64ae7-f469-6572-9559-0242ac120038',
        '1ec13d33-3f41-6cf0-b012-02420a000f18');

UPDATE public.contact
   SET telephone = NULL
 WHERE id IN ('1ea87f90-c050-60fe-9ffc-02420a000547', '1ea87f90-c063-60d2-9183-02420a000547',
              '1ea87f90-c06c-679a-99a2-02420a000547', '1eb64ae7-f469-6572-9559-0242ac120038');

CREATE UNIQUE INDEX contact_unique_phone_idx ON public.contact (telephone, tenant_group_id);


ALTER TABLE public.contact ADD user_id uuid DEFAULT NULL;
CREATE UNIQUE INDEX contact_unique_user_idx ON public.contact (user_id, tenant_group_id);

-- TODO
-- DROP TABLE person;
-- DROP TABLE organization;

SELECT public.timestampable('public.contact');

--- Users

CREATE TABLE users
    (
        id uuid NOT NULL
            PRIMARY KEY
    );
INSERT INTO users (id)
SELECT DISTINCT user_id
  FROM created_by;
--- Wallet

ALTER TABLE public.wallet ALTER COLUMN id SET DEFAULT gen_random_uuid();
ALTER TABLE public.wallet ALTER COLUMN use_in_order SET DEFAULT FALSE;
ALTER TABLE public.wallet ALTER COLUMN use_in_income SET DEFAULT FALSE;
ALTER TABLE public.wallet ALTER COLUMN show_in_layout SET DEFAULT FALSE;
ALTER TABLE public.wallet ALTER COLUMN default_in_manual_transaction SET DEFAULT FALSE;

ALTER TABLE public.wallet
    ADD CONSTRAINT wallet_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

--- Expense

ALTER TABLE public.expense RENAME TO wallet_expense;
ALTER TABLE public.wallet_expense ALTER COLUMN id SET DEFAULT gen_random_uuid();

ALTER TABLE public.wallet_expense
    ADD CONSTRAINT wallet_expense_wallet_id_fkey
        FOREIGN KEY (wallet_id) REFERENCES public.wallet (id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE public.wallet_expense
    ADD CONSTRAINT wallet_expense_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE public.wallet_expense ADD COLUMN comment text NULL;
COMMENT ON COLUMN public.wallet_expense.wallet_id IS E'Счет списания по умолчанию';

SELECT public.timestampable('public.wallet_expense');

--- Employee Penalty

CREATE TABLE public.employee_penalty
    (
        id uuid NOT NULL DEFAULT gen_random_uuid()
            PRIMARY KEY,
        name text NOT NULL,
        amount numeric(16, 2),
        comment text DEFAULT NULL,
        is_active boolean DEFAULT TRUE
    );
SELECT public.timestampable('public.employee_penalty');

INSERT INTO public.employee_penalty(name, amount, is_active)
VALUES (E'old', 0, FALSE)
;

--- Money Transfer

CREATE TABLE public.money_transfer_target
    (
        id text NOT NULL,
        name text NOT NULL,
        PRIMARY KEY (id)
    );
INSERT INTO public.money_transfer_target(id, name)
VALUES (E'wallet', E'Счёт'),
       (E'contact', E'Контакт')
;
CREATE TABLE public.money_transfer_reason
    (
        id text NOT NULL,
        name text NOT NULL,
        PRIMARY KEY (id)
    );
INSERT INTO public.money_transfer_reason(id, name)
VALUES
    --- order_prepay(order, wallet, amount, currency)
    --- wallet debit
    --- contact debit
    (E'order_prepay', E'Предоплата по заказу'),      -- C
    (E'order_prepay', E'Предоплата по заказу'),      -- W

    --- order_close(order, payment)
    --- wallet debit (payment)
    --- contact debit (payment)
    --- contact credit (order total)
    --- contact debit (employee salary)
    (E'order_debit', E'Начисление по заказу'),       -- C
    (E'order_debit', E'Начисление по заказу'),       -- W
    (E'order_credit', E'Списание по заказу'),        -- C
    (E'order_salary', E'Зарплата по заказу'),        -- C

    --- employee_payroll(contact, amount, comment)
    --- wallet credit
    --- contact credit
    (E'payroll', E'Выдача зарплаты'),                -- C
    (E'payroll', E'Выдача зарплаты'),                -- W

    --- wallet_expense(expense, wallet, amount, comment)
    --- wallet credit
    (E'expense', E'Списание по статье расходов'),    -- W

    --- employee_penalty(contact, amount, comment)
    --- contact credit
    (E'penalty', E'Штраф'),                          -- C

    --- employee_salary(contact, amount, comment)
    --- contact debit
    (E'salary', E'Начисление ежемесячного оклада'),  -- C

    --- wallet_manual(wallet, amount, comment)
    --- contact_manual(contact, amount, comment)
    (E'manual', E'Ручная проводка'),                 -- C
    (E'operand_manual', E'Ручная проводка клиента'), -- W
    (E'manual_without_wallet', E'Ручная проводка'),  -- C

    --- income_accrue(income)
    --- contact debit
    (E'income_debit', E'Начисление по поставке'),    -- C

    --- income_pay(income, wallet, amount)
    --- wallet credit
    --- contact credit
    (E'income_payment', E'Оплата за поставку'),      -- C
    (E'income_payment', E'Оплата за поставку'),      -- W

    (E'legacy', E'Какие то старые проводки'),        -- W
    (E'initial', E'Начальный баланс')                -- W
    ON CONFLICT DO NOTHING
;

CREATE TABLE public.money_transfer
    (
        id uuid DEFAULT gen_random_uuid() NOT NULL
            CONSTRAINT money_transfer_pkey
                PRIMARY KEY,
        target text NOT NULL
            CONSTRAINT money_transfer_target_fkey REFERENCES money_transfer_target ON UPDATE CASCADE ON DELETE RESTRICT,
        target_id uuid NOT NULL,
        reason text NOT NULL
            CONSTRAINT money_transfer_reason_fkey REFERENCES money_transfer_reason ON UPDATE CASCADE ON DELETE RESTRICT,
        reason_id uuid NOT NULL,
        comment text DEFAULT NULL,
        tenant_id uuid NOT NULL
            CONSTRAINT money_transfer_tenant_id_fkey REFERENCES tenant ON UPDATE RESTRICT ON DELETE RESTRICT,
        amount numeric(16, 2),
        currency varchar(3) NOT NULL,
        CHECK ( FALSE ) NO INHERIT
    );

CREATE TABLE public.money_transfer_wallet_order
    (
        FOREIGN KEY (target_id) REFERENCES public.wallet (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        FOREIGN KEY (reason_id) REFERENCES public.orders (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CHECK ( target = 'wallet' )
    )
INHERITS (money_transfer);
ALTER TABLE public.money_transfer_wallet_order ALTER target SET DEFAULT 'wallet';
CREATE TABLE public.money_transfer_contact_order
    (
        FOREIGN KEY (target_id) REFERENCES public.contact (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        FOREIGN KEY (reason_id) REFERENCES public.orders (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CHECK ( target = 'contact' )
    )
INHERITS (money_transfer);
ALTER TABLE public.money_transfer_contact_order ALTER target SET DEFAULT 'contact';


CREATE TABLE public.money_transfer_wallet_user
    (
        FOREIGN KEY (target_id) REFERENCES public.wallet (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        FOREIGN KEY (reason_id) REFERENCES public.users (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CHECK ( target = 'wallet' )
    )
INHERITS (money_transfer);
ALTER TABLE public.money_transfer_wallet_user ALTER target SET DEFAULT 'wallet';
CREATE TABLE public.money_transfer_contact_user
    (
        FOREIGN KEY (target_id) REFERENCES public.contact (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        FOREIGN KEY (reason_id) REFERENCES public.users (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CHECK ( target = 'contact' )
    )
INHERITS (money_transfer);
ALTER TABLE public.money_transfer_contact_user ALTER target SET DEFAULT 'contact';


CREATE TABLE public.money_transfer_wallet_income
    (
        FOREIGN KEY (target_id) REFERENCES public.wallet (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        FOREIGN KEY (reason_id) REFERENCES public.income (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CHECK ( target = 'wallet' )
    )
INHERITS (money_transfer);
ALTER TABLE public.money_transfer_wallet_income ALTER target SET DEFAULT 'wallet';
CREATE TABLE public.money_transfer_contact_income
    (
        FOREIGN KEY (target_id) REFERENCES public.contact (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        FOREIGN KEY (reason_id) REFERENCES public.income (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CHECK ( target = 'contact' )
    )
INHERITS (money_transfer);
ALTER TABLE public.money_transfer_contact_income ALTER target SET DEFAULT 'contact';


CREATE TABLE public.money_transfer_contact_employee_salary
    (
        FOREIGN KEY (target_id) REFERENCES public.contact (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        FOREIGN KEY (reason_id) REFERENCES public.employee_salary (id) ON UPDATE RESTRICT ON DELETE RESTRICT, --- TODO employee_salary_history with version_id
        CHECK ( target = 'contact' )
    )
INHERITS (money_transfer);
ALTER TABLE public.money_transfer_contact_employee_salary ALTER target SET DEFAULT 'contact';


CREATE TABLE public.money_transfer_contact_employee_penalty
    (
        FOREIGN KEY (target_id) REFERENCES public.contact (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        FOREIGN KEY (reason_id) REFERENCES public.employee_penalty (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CHECK ( target = 'contact' )
    )
INHERITS (money_transfer);
ALTER TABLE public.money_transfer_contact_employee_penalty ALTER target SET DEFAULT 'contact';


CREATE TABLE public.money_transfer_wallet_expense
    (
        FOREIGN KEY (target_id) REFERENCES public.wallet (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        FOREIGN KEY (reason_id) REFERENCES public.wallet_expense (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CHECK ( target = 'wallet' )
    )
INHERITS (money_transfer);
ALTER TABLE public.money_transfer_wallet_expense ALTER target SET DEFAULT 'wallet';

--- Migrate Transactions to Money Transfer

ALTER TABLE public.wallet_transaction ALTER COLUMN source TYPE text USING CASE WHEN source = 0 THEN 'legacy'
                                                                               WHEN source = 1 THEN 'order_prepay'
                                                                               WHEN source = 2 THEN 'order_debit'
                                                                               WHEN source = 3 THEN 'payroll'
                                                                               WHEN source = 4 THEN 'income_payment'
                                                                               WHEN source = 5 THEN 'expense'
                                                                               WHEN source = 6 THEN 'operand_manual'
                                                                               WHEN source = 7 THEN 'initial' END;

INSERT INTO public.money_transfer_wallet_order (id, target_id, reason, reason_id, comment, tenant_id, amount, currency)
SELECT id, wallet_id, source, source_id, description, tenant_id, amount_amount / 100, amount_currency_code
  FROM wallet_transaction
 WHERE source IN ('order_prepay', 'order_debit')
;
INSERT INTO public.money_transfer_wallet_income (id, target_id, reason, reason_id, comment, tenant_id, amount, currency)
SELECT id, wallet_id, source, source_id, description, tenant_id, amount_amount / 100, amount_currency_code
  FROM wallet_transaction
 WHERE source IN ('income_payment')
;
INSERT INTO public.money_transfer_wallet_expense (id, target_id, reason, reason_id, comment, tenant_id, amount,
                                                  currency)
SELECT id, wallet_id, source, source_id, description, tenant_id, amount_amount / 100, amount_currency_code
  FROM wallet_transaction
 WHERE source IN ('expense')
;
INSERT INTO public.money_transfer_wallet_user (id, target_id, reason, reason_id, comment, tenant_id, amount, currency)
SELECT id, wallet_id, source, (SELECT user_id FROM created_by cb WHERE cb.id = id LIMIT 1), description, tenant_id,
       amount_amount / 100, amount_currency_code
  FROM wallet_transaction
 WHERE source IN ('legacy', 'payroll', 'operand_manual', 'initial')
;

ALTER TABLE customer_transaction ALTER source TYPE text USING CASE WHEN source = 1 THEN 'order_prepay'
                                                                   WHEN source = 2 THEN 'order_debit'
                                                                   WHEN source = 3 THEN 'order_credit'
                                                                   WHEN source = 4 THEN 'order_salary'
                                                                   WHEN source = 5 THEN 'payroll'
                                                                   WHEN source = 6 THEN 'income_debit'
                                                                   WHEN source = 7 THEN 'income_payment'
                                                                   WHEN source = 8 THEN 'salary'
                                                                   WHEN source = 9 THEN 'penalty'
                                                                   WHEN source = 10 THEN 'manual'
                                                                   WHEN source = 11 THEN 'manual_without_wallet'
                                                                   ELSE 'unknown' END
;

INSERT INTO public.money_transfer_contact_order (id, target_id, reason, reason_id, comment, tenant_id, amount, currency)
SELECT id, operand_id, source, source_id, description, tenant_id, amount_amount / 100, amount_currency_code
  FROM customer_transaction
 WHERE source IN ('order_prepay', 'order_debit', 'order_credit', 'order_salary')
;
INSERT INTO public.money_transfer_contact_income (id, target_id, reason, reason_id, comment, tenant_id, amount,
                                                  currency)
SELECT id, operand_id, source, source_id, description, tenant_id, amount_amount / 100, amount_currency_code
  FROM customer_transaction
 WHERE source IN ('income_debit', 'income_payment')
;
--- TODO Migrate Salary after add employee_salary_history
INSERT INTO public.money_transfer_contact_employee_penalty (id, target_id, reason, reason_id, comment, tenant_id,
                                                            amount, currency)
SELECT id, operand_id, source, (SELECT id FROM employee_penalty WHERE name = 'old'), description, tenant_id,
       amount_amount / 100, amount_currency_code
  FROM customer_transaction
 WHERE source IN ('penalty')
;
INSERT INTO public.money_transfer_contact_user (id, target_id, reason, reason_id, comment, tenant_id, amount, currency)
SELECT id, operand_id, source, (SELECT user_id FROM created_by cb WHERE cb.id = id LIMIT 1), description, tenant_id,
       amount_amount / 100, amount_currency_code
  FROM customer_transaction
 WHERE source IN ('payroll', 'manual', 'manual_without_wallet')
;

--- Balance

ALTER TABLE public.wallet
    ADD COLUMN balance numeric(16, 2) DEFAULT 0;

ALTER TABLE public.wallet
    ADD COLUMN balance_at timestamptz DEFAULT NOW();

ALTER TABLE public.contact
    ADD COLUMN balance numeric(16, 2) DEFAULT 0;

ALTER TABLE public.contact
    ADD COLUMN balance_at timestamptz DEFAULT NOW();

CREATE OR REPLACE FUNCTION public.set_money_transfer_balance_trigger() RETURNS trigger AS
$$
BEGIN
    IF new.target_id <> old.target_id THEN CALL set_balance(('public.' || new.target)::regclass, old.target_id); END IF;

    CALL set_balance(('public.' || new.target)::regclass, new.target_id);

    RETURN new;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE PROCEDURE public.create_balance_trigger(regclass) AS
$$
BEGIN
    EXECUTE 'CREATE TRIGGER set_balance_on_' || $1 || '_trigger
                AFTER INSERT OR DELETE OR UPDATE OF amount,target_id ON ' || $1 || ' FOR EACH ROW
                EXECUTE PROCEDURE set_money_transfer_balance_trigger()';
END ;
$$ LANGUAGE plpgsql;

CALL public.create_balance_trigger('public.money_transfer_wallet_expense');
CALL public.create_balance_trigger('public.money_transfer_wallet_income');
CALL public.create_balance_trigger('public.money_transfer_wallet_order');
CALL public.create_balance_trigger('public.money_transfer_wallet_user');

CALL public.create_balance_trigger('public.money_transfer_contact_employee_penalty');
CALL public.create_balance_trigger('public.money_transfer_contact_employee_salary');
CALL public.create_balance_trigger('public.money_transfer_contact_income');
CALL public.create_balance_trigger('public.money_transfer_contact_order');
CALL public.create_balance_trigger('public.money_transfer_contact_user');

SELECT set_balance('public.wallet', id)
  FROM public.wallet;
SELECT set_balance('public.contact', id)
  FROM public.contact;

SELECT public.timestampable('public.wallet');
SELECT public.timestampable('public.money_transfer');

--- Warehouse

ALTER TABLE public.warehouse ALTER COLUMN id SET DEFAULT gen_random_uuid();
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

SELECT public.timestampable('public.warehouse');

--- Vehicle

ALTER TABLE public.car
    RENAME TO vehicle;

ALTER TABLE public.vehicle RENAME description TO comment;
ALTER TABLE public.vehicle RENAME gosnomer TO legal_plate;

ALTER TABLE public.vehicle
    ADD CONSTRAINT vehicle_tenant_group_id_fkey
        FOREIGN KEY (tenant_group_id) REFERENCES public.tenant_group (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

SELECT public.timestampable('public.vehicle');

ALTER TABLE public.vehicle ADD CONSTRAINT vehicle_identifier_tenant_group_id_key
    UNIQUE (identifier, tenant_group_id);
DROP INDEX IF EXISTS uniq_773de69d772e836adff2bbb0;

--- Vehicle Body

ALTER TABLE public.vehicle_model
    RENAME TO vehicle_body;

ALTER TABLE public.vehicle RENAME vehicle_id TO vehicle_body_id;
ALTER TABLE public.vehicle
    ADD CONSTRAINT vehicle_vehicle_body_id_fkey
        FOREIGN KEY (vehicle_body_id) REFERENCES public.vehicle_body (id) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE public.vehicle_body
    ADD CONSTRAINT vehicle_body_manufacturer_id_fkey
        FOREIGN KEY (manufacturer_id) REFERENCES public.manufacturer (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.vehicle_body ALTER COLUMN id SET DEFAULT gen_random_uuid();

SELECT public.timestampable('public.vehicle_body');

--- Vehicle Body Type
CREATE TABLE public.vehicle_body_type
    (
        id text NOT NULL,
        name text NOT NULL,
        PRIMARY KEY (id)
    );
INSERT INTO public.vehicle_body_type(id, name)
VALUES (E'unknown', 'Неопределён'),
       (E'sedan', 'Седан'),
       (E'hatchback', 'Хэтчбек'),
       (E'liftback', 'Лифтбек'),
       (E'allroad', 'Внедорожник'),
       (E'wagon', 'Универсал'),
       (E'coupe', 'Купе'),
       (E'minivan', 'Минивэн'),
       (E'pickup', 'Пикап'),
       (E'limousine', 'Лимузин'),
       (E'van', 'Фургон'),
       (E'cabrio', 'Кабриолет')
;
ALTER TABLE public.vehicle ADD COLUMN vehicle_body_type_id text DEFAULT NULL;
ALTER TABLE public.vehicle
    ADD CONSTRAINT vehicle_vehicle_body_type_id_fkey
        FOREIGN KEY (vehicle_body_type_id) REFERENCES public.vehicle_body_type (id) ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE public.vehicle
   SET vehicle_body_type_id = CASE WHEN case_type = 0 THEN 'unknown'
                                   WHEN case_type = 1 THEN 'sedan'
                                   WHEN case_type = 2 THEN 'hatchback'
                                   WHEN case_type = 3 THEN 'liftback'
                                   WHEN case_type = 4 THEN 'allroad'
                                   WHEN case_type = 5 THEN 'wagon'
                                   WHEN case_type = 6 THEN 'coupe'
                                   WHEN case_type = 7 THEN 'minivan'
                                   WHEN case_type = 8 THEN 'pickup'
                                   WHEN case_type = 9 THEN 'limousine'
                                   WHEN case_type = 10 THEN 'van'
                                   WHEN case_type = 11 THEN 'cabrio'
                                   ELSE 'unknown' END
;
ALTER TABLE public.vehicle ALTER COLUMN vehicle_body_type_id SET NOT NULL;
ALTER TABLE public.vehicle DROP COLUMN case_type;

--- Vehicle Transmission

CREATE TABLE public.vehicle_transmission
    (
        id text NOT NULL,
        name text NOT NULL,
        PRIMARY KEY (id)
    );
INSERT INTO public.vehicle_transmission(id, name)
VALUES (E'unknown', 'Неопределена'),
       (E'AT', 'Автоматическая'),
       (E'AMT', 'Робот'),
       (E'CVT', 'Вариатор'),
       (E'MT', 'Механическая'),
       (E'AT5', 'Автоматическая (5 ступеней)'),
       (E'AT7', 'Автоматическая (7 ступеней)')
;
ALTER TABLE public.vehicle ADD COLUMN transmission text DEFAULT NULL;
ALTER TABLE public.vehicle
    ADD CONSTRAINT vehicle_vehicle_transmission_id_fkey
        FOREIGN KEY (transmission) REFERENCES public.vehicle_transmission (id) ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE public.vehicle
   SET transmission = CASE WHEN equipment_transmission = 0 THEN 'unknown'
                           WHEN equipment_transmission = 1 THEN 'AT'
                           WHEN equipment_transmission = 2 THEN 'AMT'
                           WHEN equipment_transmission = 3 THEN 'CVT'
                           WHEN equipment_transmission = 4 THEN 'MT'
                           WHEN equipment_transmission = 5 THEN 'AT5'
                           WHEN equipment_transmission = 6 THEN 'AT7'
                           ELSE 'unknown' END
;
ALTER TABLE public.vehicle DROP equipment_transmission;

--- Vehicle Air Intake
CREATE TABLE public.vehicle_air_intake
    (
        id text NOT NULL,
        name text NOT NULL,
        PRIMARY KEY (id)
    );
INSERT INTO public.vehicle_air_intake(id, name)
VALUES (E'unknown', 'Неопределён'),
       (E'atmo', 'Атмосферный'),
       (E'turbo', 'Турбированный')
;
ALTER TABLE public.vehicle ADD COLUMN air_intake text DEFAULT NULL;
ALTER TABLE public.vehicle
    ADD CONSTRAINT vehicle_vehicle_air_intake_id_fkey
        FOREIGN KEY (air_intake) REFERENCES public.vehicle_air_intake (id) ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE public.vehicle
   SET air_intake = CASE WHEN equipment_engine_air_intake = 0 THEN 'unknown'
                         WHEN equipment_engine_air_intake = 1 THEN 'atmo'
                         WHEN equipment_engine_air_intake = 2 THEN 'turbo'
                         ELSE 'unknown' END
;

ALTER TABLE public.vehicle DROP equipment_engine_air_intake;

--- Vehicle Drive Wheel
CREATE TABLE public.vehicle_drive_wheel
    (
        id text NOT NULL,
        name text NOT NULL,
        PRIMARY KEY (id)
    );
INSERT INTO public.vehicle_drive_wheel(id, name)
VALUES (E'unknown', 'Неопределён'),
       (E'FWD', 'Передний'),
       (E'RWD', 'Задний'),
       (E'AWD', 'Полный')
;
ALTER TABLE public.vehicle ADD COLUMN drive_wheel text DEFAULT NULL;
ALTER TABLE public.vehicle
    ADD CONSTRAINT vehicle_vehicle_drive_wheel_id_fkey
        FOREIGN KEY (drive_wheel) REFERENCES public.vehicle_drive_wheel (id) ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE public.vehicle
   SET drive_wheel = CASE WHEN equipment_wheel_drive = 0 THEN 'unknown'
                          WHEN equipment_wheel_drive = 1 THEN 'FWD'
                          WHEN equipment_wheel_drive = 2 THEN 'RWD'
                          WHEN equipment_wheel_drive = 3 THEN 'AWD'
                          ELSE 'unknown' END
;

ALTER TABLE public.vehicle DROP equipment_wheel_drive;

--- Vehicle Fuel Type
CREATE TABLE public.vehicle_fuel_type
    (
        id text NOT NULL,
        name text NOT NULL,
        PRIMARY KEY (id)
    );
INSERT INTO public.vehicle_fuel_type(id, name)
VALUES (E'unknown', 'Неопределён'),
       (E'petrol', 'Бензин'),
       (E'diesel', 'Дизель'),
       (E'ethanol', 'Этанол'),
       (E'electric', 'Электрический'),
       (E'hybrid', 'Гибрид')
;
ALTER TABLE public.vehicle ADD COLUMN fuel_type text DEFAULT NULL;
ALTER TABLE public.vehicle
    ADD CONSTRAINT vehicle_vehicle_fuel_type_id_fkey
        FOREIGN KEY (fuel_type) REFERENCES public.vehicle_fuel_type (id) ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE public.vehicle
   SET fuel_type = CASE WHEN equipment_engine_type = 0 THEN 'unknown'
                        WHEN equipment_engine_type = 1 THEN 'petrol'
                        WHEN equipment_engine_type = 2 THEN 'diesel'
                        WHEN equipment_engine_type = 3 THEN 'ethanol'
                        WHEN equipment_engine_type = 4 THEN 'electric'
                        WHEN equipment_engine_type = 5 THEN 'hybrid'
                        ELSE 'unknown' END
;

ALTER TABLE public.vehicle DROP equipment_engine_type;

--- Vehicle Injection
CREATE TABLE public.vehicle_injection
    (
        id text NOT NULL,
        name text NOT NULL,
        PRIMARY KEY (id)
    );
INSERT INTO public.vehicle_injection(id, name)
VALUES (E'unknown', 'Неопределён'),
       (E'classic', 'Классический'),
       (E'direct', 'Непосредственный впрыск')
;
ALTER TABLE public.vehicle ADD COLUMN injection text DEFAULT NULL;
ALTER TABLE public.vehicle
    ADD CONSTRAINT vehicle_vehicle_injection_id_fkey
        FOREIGN KEY (injection) REFERENCES public.vehicle_injection (id) ON UPDATE CASCADE ON DELETE RESTRICT;
UPDATE public.vehicle
   SET injection = CASE WHEN equipment_engine_injection = 0 THEN 'unknown'
                        WHEN equipment_engine_injection = 1 THEN 'classic'
                        WHEN equipment_engine_injection = 2 THEN 'direct'
                        ELSE 'unknown' END
;

ALTER TABLE public.vehicle DROP equipment_engine_injection;

--- Vehicle Tire Fitting Category
CREATE TABLE public.vehicle_tire_category
    (
        id text NOT NULL,
        name text NOT NULL,
        PRIMARY KEY (id)
    );
INSERT INTO public.vehicle_tire_category(id, name)
VALUES (E'unknown', 'Неопределён'),
       (E'car', 'Легковая'),
       (E'suv', 'Внедорожник'),
       (E'crossover', 'Кроссовер'),
       (E'minivan', 'Минивен')
;
ALTER TABLE public.vehicle_body ADD COLUMN tire_category text DEFAULT NULL;
ALTER TABLE public.vehicle_body
    ADD CONSTRAINT vehicle_body_vehicle_tire_category_id_fkey
        FOREIGN KEY (tire_category) REFERENCES public.vehicle_tire_category (id) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE public.vehicle ADD COLUMN tire_category text DEFAULT NULL;
ALTER TABLE public.vehicle
    ADD CONSTRAINT vehicle_vehicle_tire_category_id_fkey
        FOREIGN KEY (tire_category) REFERENCES public.vehicle_tire_category (id) ON UPDATE CASCADE ON DELETE RESTRICT;

--- Vehicle Engine

ALTER TABLE public.vehicle RENAME equipment_engine_name TO engine_name;
ALTER TABLE public.vehicle RENAME equipment_engine_capacity TO engine_capacity;

--- Vehicle Contact

CREATE TABLE public.vehicle_contact
    (
        id uuid NOT NULL DEFAULT gen_random_uuid(),
        vehicle_id uuid NOT NULL,
        contact_id uuid NOT NULL,
        comment text,
        is_actual bool NOT NULL DEFAULT TRUE,
        PRIMARY KEY (id),
        FOREIGN KEY (vehicle_id) REFERENCES public.vehicle (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        FOREIGN KEY (contact_id) REFERENCES public.contact (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        UNIQUE (vehicle_id, contact_id)
    );
SELECT public.timestampable('public.vehicle_contact');

--- Part Case

ALTER TABLE public.part_case RENAME TO part_vehicle_body;
ALTER TABLE public.part_vehicle_body RENAME vehicle_id TO vehicle_body_id;

ALTER TABLE public.part_vehicle_body ALTER COLUMN id SET DEFAULT gen_random_uuid();

ALTER TABLE public.part_vehicle_body
    ADD CONSTRAINT part_vehicle_body_part_id_fkey
        FOREIGN KEY (part_id) REFERENCES public.part (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.part_vehicle_body
    ADD CONSTRAINT part_vehicle_body_vehicle_body_id_fkey
        FOREIGN KEY (vehicle_body_id) REFERENCES public.vehicle_body (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

SELECT public.timestampable('public.part_vehicle_body');

--- MC Equipment

DROP INDEX IF EXISTS idx_2b65786f4d7b7542;
DROP INDEX IF EXISTS idx_b37ebc5fbb3453db;
DROP INDEX IF EXISTS idx_b37ebc5f517fe9fe;

ALTER TABLE public.mc_equipment RENAME vehicle_id TO vehicle_body_id;
ALTER TABLE public.mc_equipment
    ADD CONSTRAINT mc_equipment_vehicle_id_fkey
        FOREIGN KEY (vehicle_body_id) REFERENCES public.vehicle_body (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.mc_equipment
    ADD CONSTRAINT mc_equipment_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.mc_equipment ADD COLUMN is_published boolean NOT NULL DEFAULT 'FALSE';
UPDATE public.mc_equipment t
   SET is_published = p.published
  FROM public.publish_view p
 WHERE p.id = t.id;
DROP VIEW public.publish_view;
DROP TABLE public.publish;

--- MC Equipment Transmission

ALTER TABLE public.mc_equipment ADD COLUMN transmission text DEFAULT NULL;
ALTER TABLE public.mc_equipment
    ADD CONSTRAINT mc_equipment_vehicle_transmission_id_fkey
        FOREIGN KEY (transmission) REFERENCES public.vehicle_transmission (id) ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE public.mc_equipment
   SET transmission = CASE WHEN equipment_transmission = 0 THEN 'unknown'
                           WHEN equipment_transmission = 1 THEN 'AT'
                           WHEN equipment_transmission = 2 THEN 'AMT'
                           WHEN equipment_transmission = 3 THEN 'CVT'
                           WHEN equipment_transmission = 4 THEN 'MT'
                           WHEN equipment_transmission = 5 THEN 'AT5'
                           WHEN equipment_transmission = 6 THEN 'AT7'
                           ELSE 'unknown' END
;
ALTER TABLE public.mc_equipment DROP equipment_transmission;

--- MC Equipment Drive Wheel

ALTER TABLE public.mc_equipment ADD COLUMN drive_wheel text DEFAULT NULL;
ALTER TABLE public.mc_equipment
    ADD CONSTRAINT mc_equipment_vehicle_drive_wheel_id_fkey
        FOREIGN KEY (drive_wheel) REFERENCES public.vehicle_drive_wheel (id) ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE public.mc_equipment
   SET drive_wheel = CASE WHEN equipment_wheel_drive = 0 THEN 'unknown'
                          WHEN equipment_wheel_drive = 1 THEN 'FWD'
                          WHEN equipment_wheel_drive = 2 THEN 'RWD'
                          WHEN equipment_wheel_drive = 3 THEN 'AWD'
                          ELSE 'unknown' END
;

ALTER TABLE public.mc_equipment DROP equipment_wheel_drive;

--- MC Equipment Engine

ALTER TABLE public.mc_equipment RENAME equipment_engine_name TO engine_name;
ALTER TABLE public.mc_equipment RENAME equipment_engine_capacity TO engine_capacity;

--- MC Equipment Fuel Type

ALTER TABLE public.mc_equipment ADD COLUMN fuel_type text DEFAULT NULL;
ALTER TABLE public.mc_equipment
    ADD CONSTRAINT mc_equipment_vehicle_fuel_type_id_fkey
        FOREIGN KEY (fuel_type) REFERENCES public.vehicle_fuel_type (id) ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE public.mc_equipment
   SET fuel_type = CASE WHEN equipment_engine_type = 0 THEN 'unknown'
                        WHEN equipment_engine_type = 1 THEN 'petrol'
                        WHEN equipment_engine_type = 2 THEN 'diesel'
                        WHEN equipment_engine_type = 3 THEN 'ethanol'
                        WHEN equipment_engine_type = 4 THEN 'electric'
                        WHEN equipment_engine_type = 5 THEN 'hybrid'
                        ELSE 'unknown' END
;

ALTER TABLE public.mc_equipment DROP equipment_engine_type;

--- MC Equipment Injection

ALTER TABLE public.mc_equipment ADD COLUMN injection text DEFAULT NULL;
ALTER TABLE public.mc_equipment
    ADD CONSTRAINT mc_equipment_vehicle_injection_id_fkey
        FOREIGN KEY (injection) REFERENCES public.vehicle_injection (id) ON UPDATE CASCADE ON DELETE RESTRICT;
UPDATE public.mc_equipment
   SET injection = CASE WHEN equipment_engine_injection = 0 THEN 'unknown'
                        WHEN equipment_engine_injection = 1 THEN 'classic'
                        WHEN equipment_engine_injection = 2 THEN 'direct'
                        ELSE 'unknown' END
;

ALTER TABLE public.mc_equipment DROP equipment_engine_injection;

--- MC Equipment Air Intake

ALTER TABLE public.mc_equipment ADD COLUMN air_intake text DEFAULT NULL;
ALTER TABLE public.mc_equipment
    ADD CONSTRAINT mc_equipment_vehicle_air_intake_id_fkey
        FOREIGN KEY (air_intake) REFERENCES public.vehicle_air_intake (id) ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE public.mc_equipment
   SET air_intake = CASE WHEN equipment_engine_air_intake = 0 THEN 'unknown'
                         WHEN equipment_engine_air_intake = 1 THEN 'atmo'
                         WHEN equipment_engine_air_intake = 2 THEN 'turbo'
                         ELSE 'unknown' END
;

ALTER TABLE public.mc_equipment DROP equipment_engine_air_intake;

--- MC Line

ALTER TABLE public.mc_line RENAME COLUMN recommended TO is_recommended;

ALTER TABLE public.mc_line DROP CONSTRAINT fk_b37ebc5fbb3453db;

ALTER TABLE public.mc_line DROP CONSTRAINT fk_b37ebc5f517fe9fe;

ALTER TABLE public.mc_line
    ADD CONSTRAINT mc_line_equipment_id_fkey
        FOREIGN KEY (equipment_id) REFERENCES public.mc_equipment (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.mc_line
    ADD CONSTRAINT mc_line_work_id_fkey
        FOREIGN KEY (work_id) REFERENCES public.mc_work (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.mc_line ADD COLUMN is_published boolean NOT NULL DEFAULT 'FALSE';
UPDATE public.mc_line l
   SET is_published = e.is_published
  FROM mc_equipment e
 WHERE e.id = l.equipment_id;

ALTER TABLE public.mc_line
    ADD CONSTRAINT mc_line_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

--- MC Part

ALTER TABLE public.mc_part DROP CONSTRAINT fk_2b65786f4d7b7542;

ALTER TABLE public.mc_part
    ADD CONSTRAINT mc_part_line_id_fkey
        FOREIGN KEY (line_id) REFERENCES public.mc_line (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.mc_part
    ADD CONSTRAINT mc_part_part_id_fkey
        FOREIGN KEY (part_id) REFERENCES public.part (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.mc_part ADD COLUMN is_published boolean NOT NULL DEFAULT 'FALSE';
UPDATE public.mc_part p
   SET is_published = l.is_published
  FROM mc_line l
 WHERE p.line_id = l.id;

ALTER TABLE public.mc_part
    ADD CONSTRAINT mc_part_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.mc_part ALTER COLUMN quantity TYPE numeric(16, 2) USING quantity / 100;

--- MC Work

ALTER TABLE public.mc_work
    ADD CONSTRAINT mc_work_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.mc_work ALTER COLUMN price_amount TYPE numeric(16, 2) USING price_amount / 100;

--- MC Timestampable

SELECT public.timestampable('public.mc_equipment');
SELECT public.timestampable('public.mc_line');
SELECT public.timestampable('public.mc_part');
SELECT public.timestampable('public.mc_work');

--- Order

DROP SEQUENCE order_number_seq;
DROP INDEX IF EXISTS uniq_e52ffdee96901f549033212a;
DROP INDEX IF EXISTS idx_e52ffdee6b20ba36;

ALTER TABLE public.orders ADD CONSTRAINT orders_number_tenant_id_key
    UNIQUE (number, tenant_id);

ALTER TABLE public.orders DROP CONSTRAINT fk_e52ffdee6b20ba36;

ALTER TABLE public.orders
    ADD CONSTRAINT orders_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.orders RENAME COLUMN car_id TO vehicle_id;
ALTER TABLE public.orders RENAME COLUMN description TO comment;
ALTER TABLE public.orders ADD COLUMN contact_gave_id uuid NULL;
ALTER TABLE public.orders ADD COLUMN contact_took_id uuid NULL;
ALTER TABLE public.orders ADD COLUMN contact_paid_id uuid NULL;

COMMENT ON COLUMN public.orders.contact_gave_id IS E'Кто привёз автомобиль';
COMMENT ON COLUMN public.orders.contact_took_id IS E'Кто забрал автомобиль';
COMMENT ON COLUMN public.orders.contact_paid_id IS E'Плательщик';

ALTER TABLE public.orders
    ADD CONSTRAINT orders_contact_gave_fkey
        FOREIGN KEY (contact_gave_id) REFERENCES public.contact (id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE public.orders
    ADD CONSTRAINT orders_contact_took_fkey
        FOREIGN KEY (contact_took_id) REFERENCES public.contact (id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE public.orders
    ADD CONSTRAINT orders_contact_paid_fkey
        FOREIGN KEY (contact_paid_id) REFERENCES public.contact (id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE public.orders
    ADD CONSTRAINT orders_vehicle_id_fkey
        FOREIGN KEY (vehicle_id) REFERENCES public.vehicle (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

UPDATE public.orders
   SET contact_gave_id = customer_id,
       contact_took_id = customer_id,
       contact_paid_id = customer_id
 WHERE TRUE;
ALTER TABLE public.orders DROP COLUMN customer_id;

CREATE TABLE public.order_status
    (
        id text NOT NULL,
        name text NOT NULL,
        color text NOT NULL,
        PRIMARY KEY (id)
    );
INSERT INTO public.order_status(id, name, color)
VALUES (E'draft', 'Черновик', 'default'),
       (E'scheduling', 'Ожидание по записи', 'primary'),
       (E'ordering', 'Заказ запчастей', 'danger'),
       (E'matching', 'Согласование', 'warning'),
       (E'tracking', 'Ожидание запчастей', 'default'),
       (E'delivery', 'Требуется доставка', 'info'),
       (E'notification', 'Уведомление клиента', 'warning'),
       (E'working', 'В работе', 'success'),
       (E'ready', 'Ожидает выдачи', 'primary'),
       (E'closed', 'Закрыт', 'default'),
       (E'selection', 'Подбор запчастей', 'danger'),
       (E'payment_waiting', 'Ожидает Оплаты', 'primary'),
       (E'cancelled', 'Отменён', 'default')
;
ALTER TABLE public.orders ALTER status TYPE text USING CASE WHEN status = 1 THEN 'draft'
                                                            WHEN status = 2 THEN 'scheduling'
                                                            WHEN status = 3 THEN 'ordering'
                                                            WHEN status = 4 THEN 'matching'
                                                            WHEN status = 5 THEN 'tracking'
                                                            WHEN status = 6 THEN 'delivery'
                                                            WHEN status = 7 THEN 'notification'
                                                            WHEN status = 8 THEN 'working'
                                                            WHEN status = 9 THEN 'ready'
                                                            WHEN status = 10 THEN 'closed'
                                                            WHEN status = 11 THEN 'selection'
                                                            WHEN status = 12 THEN 'payment_waiting'
                                                            WHEN status = 13 THEN 'cancelled'
                                                            ELSE 'unknown' END
;
ALTER TABLE public.orders RENAME status TO status_id;
ALTER TABLE public.orders
    ADD CONSTRAINT orders_order_status_id_fkey
        FOREIGN KEY (status_id) REFERENCES public.order_status (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.orders ALTER COLUMN id SET DEFAULT gen_random_uuid();
ALTER TABLE public.orders ALTER COLUMN status_id SET DEFAULT 'draft';


CREATE OR REPLACE FUNCTION set_orders_number() RETURNS trigger AS
$$
BEGIN
    new.number := COALESCE((SELECT MAX(number) + 1 FROM public.orders o WHERE o.tenant_id = new.tenant_id), 1);

    RETURN new;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER set_orders_number_trigger
    BEFORE INSERT
    ON public.orders
    FOR EACH ROW
EXECUTE PROCEDURE set_orders_number();

UPDATE public.orders o
   SET worker_id = e.person_id
  FROM employee e
 WHERE e.id = o.worker_id;
ALTER TABLE public.orders
    ADD CONSTRAINT orders_contact_worker_id_fkey
        FOREIGN KEY (worker_id) REFERENCES public.contact (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

SELECT public.timestampable('public.orders');

--- Part Transfer

CREATE TABLE public.part_transfer_reason
    (
        id text NOT NULL,
        name text NOT NULL,
        PRIMARY KEY (id)
    );
INSERT INTO public.part_transfer_reason(id, name)
VALUES (E'manual', E'Ручная проводка'),
       (E'income', E'Поступление по приходу'),
       (E'order', E'Списание по заказу')
;

CREATE TABLE public.part_transfer
    (
        id uuid DEFAULT gen_random_uuid() NOT NULL
            CONSTRAINT part_transfer_pkey
                PRIMARY KEY,
        part_id uuid NOT NULL
            CONSTRAINT part_transfer_part_fkey REFERENCES part (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        reason text NOT NULL
            CONSTRAINT part_transfer_reason_fkey REFERENCES part_transfer_reason ON UPDATE CASCADE ON DELETE RESTRICT,
        reason_id uuid NOT NULL,
        comment text DEFAULT NULL,
        tenant_id uuid NOT NULL
            CONSTRAINT part_transfer_tenant_id_fkey REFERENCES tenant ON UPDATE RESTRICT ON DELETE RESTRICT,
        quantity numeric(10, 2),
        CHECK ( FALSE ) NO INHERIT
    );

CREATE TABLE public.part_transfer_user
    (
        FOREIGN KEY (reason_id) REFERENCES public.users (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CHECK ( reason = 'manual' )
    )
INHERITS (part_transfer);
ALTER TABLE public.part_transfer_user ALTER reason SET DEFAULT 'manual';

CREATE TABLE public.part_transfer_income
    (
        FOREIGN KEY (reason_id) REFERENCES public.income_part (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CHECK ( reason = 'income' )
    )
INHERITS (part_transfer);
ALTER TABLE public.part_transfer_income ALTER reason SET DEFAULT 'income';

CREATE TABLE public.part_transfer_order
    (
        FOREIGN KEY (reason_id) REFERENCES public.orders (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CHECK ( reason = 'order' )
    )
INHERITS (part_transfer);
ALTER TABLE public.part_transfer_order ALTER reason SET DEFAULT 'order';


INSERT INTO public.part_transfer_user (id, part_id, reason_id, comment, tenant_id, quantity)
SELECT id, part_id, source_id, description, tenant_id, quantity / 100
  FROM motion
 WHERE source_type = 1;
INSERT INTO public.part_transfer_income (id, part_id, reason_id, comment, tenant_id, quantity)
SELECT id, part_id, source_id, description, tenant_id, quantity / 100
  FROM motion
 WHERE source_type = 2;
INSERT INTO public.part_transfer_order (id, part_id, reason_id, comment, tenant_id, quantity)
SELECT id, part_id, source_id, description, tenant_id, quantity / 100
  FROM motion
 WHERE source_type = 3;

SELECT public.timestampable('public.part_transfer');


--- Income

ALTER TABLE public.income ALTER COLUMN id SET DEFAULT gen_random_uuid();

ALTER TABLE public.income DROP old_id;
ALTER TABLE public.income ADD amount numeric(16, 2) NOT NULL DEFAULT 0;
ALTER TABLE public.income ADD accrued_at timestamptz DEFAULT NULL;
ALTER TABLE public.income ADD comment text DEFAULT NULL;

UPDATE public.income
   SET amount = v.amount,
       accrued_at = v.created_at
  FROM (SELECT ia.income_id, ia.amount_amount / 100 AS amount, cb.created_at
          FROM income_accrue ia
                   JOIN created_by cb
                   ON cb.id = ia.id) v
 WHERE id = v.income_id
;

ALTER TABLE public.income
    ADD CONSTRAINT income_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

SELECT public.timestampable('public.income');

--- Income Part

ALTER TABLE public.income_part ALTER COLUMN id SET DEFAULT gen_random_uuid();

ALTER TABLE public.income_part ALTER COLUMN quantity TYPE numeric(10, 2) USING quantity / 100;
ALTER TABLE public.income_part ALTER COLUMN price_amount TYPE numeric(16, 2) USING price_amount / 100;
ALTER TABLE public.income_part RENAME price_amount TO amount;
ALTER TABLE public.income_part DROP price_currency_code;
ALTER TABLE public.income_part ADD comment text DEFAULT NULL;

ALTER TABLE public.income_part
    ADD CONSTRAINT income_part_income_id_fkey
        FOREIGN KEY (income_id) REFERENCES public.income (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE public.income_part
    ADD CONSTRAINT income_part_part_id_fkey
        FOREIGN KEY (part_id) REFERENCES public.part (id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE public.income_part DROP CONSTRAINT fk_834566e8640ed2c0;

ALTER TABLE public.income_part
    ADD CONSTRAINT income_part_tenant_id_fkey
        FOREIGN KEY (tenant_id) REFERENCES public.tenant (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

SELECT public.timestampable('public.income_part');

--- Income Amount

CREATE OR REPLACE FUNCTION public.set_income_amount(income_id uuid) RETURNS void AS
$$
BEGIN
    UPDATE public.income
       SET amount = (SELECT SUM(sub.amount)
                       FROM (SELECT ip.amount * ip.quantity AS amount FROM income_part ip WHERE ip.income_id = $1) sub)
     WHERE income.id = $1;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.set_income_amount_trigger() RETURNS trigger AS
$$
BEGIN
    IF new.income_id <> old.income_id THEN PERFORM public.set_income_amount(old.income_id); END IF;

    PERFORM public.set_income_amount(new.income_id);

    RETURN new;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER set_income_amount_on_insert_or_update
    AFTER INSERT OR UPDATE OF amount, quantity, income_id
    ON public.income_part
    FOR EACH ROW
EXECUTE PROCEDURE public.set_income_amount_trigger();

--- Income Items

ALTER TABLE public.income ADD items int DEFAULT 0;

CREATE OR REPLACE FUNCTION public.set_income_items(income_id uuid) RETURNS void AS
$$
BEGIN
    UPDATE public.income
       SET items = (SELECT COUNT(id) FROM income_part ip WHERE ip.income_id = $1)
     WHERE income.id = $1;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION public.set_income_items_trigger() RETURNS trigger AS
$$
BEGIN
    PERFORM public.set_income_items(new.income_id);

    RETURN new;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER set_income_items_trigger
    AFTER INSERT OR DELETE
    ON public.income_part
    FOR EACH ROW
EXECUTE PROCEDURE public.set_income_items_trigger();

SELECT public.set_income_items(id)
  FROM income;

--- Income Drop

DROP TABLE public.income_accrue;
