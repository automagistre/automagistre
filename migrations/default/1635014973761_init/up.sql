DROP VIEW public.appeal_view;;
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

ALTER TABLE "public"."manufacturer"
    ALTER COLUMN "id" SET DEFAULT gen_random_uuid();

ALTER TABLE "public"."manufacturer"
    DROP COLUMN "logo" CASCADE;

ALTER TABLE "public"."manufacturer"
    ADD COLUMN "created_at" timestamptz NOT NULL DEFAULT NOW();

ALTER TABLE "public"."manufacturer"
    ADD COLUMN "updated_at" timestamptz NOT NULL DEFAULT NOW();

CREATE OR REPLACE FUNCTION "public"."set_current_timestamp_updated_at"() RETURNS TRIGGER AS
$$
DECLARE
    _new record;
BEGIN
    _new := new;
    _new."updated_at" = NOW();
    RETURN _new;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER "set_public_manufacturer_updated_at"
    BEFORE UPDATE
    ON "public"."manufacturer"
    FOR EACH ROW
EXECUTE PROCEDURE "public"."set_current_timestamp_updated_at"();
COMMENT ON TRIGGER "set_public_manufacturer_updated_at" ON "public"."manufacturer" IS 'trigger to set value of column "updated_at" to current timestamp on row update';

---

UPDATE public.manufacturer
SET created_at = cb.created_at,
    updated_at = cb.created_at
FROM public.created_by cb
WHERE cb.id = manufacturer.id;

SELECT audit.audit_table('public.manufacturer');

--- Vehicle

ALTER TABLE "public"."vehicle_model"
    RENAME TO "vehicle";

ALTER TABLE "public"."vehicle"
    ADD COLUMN "created_at" timestamptz NOT NULL DEFAULT NOW();

ALTER TABLE "public"."vehicle"
    ADD COLUMN "updated_at" timestamptz NOT NULL DEFAULT NOW();

CREATE OR REPLACE FUNCTION "public"."set_current_timestamp_updated_at"() RETURNS TRIGGER AS
$$
DECLARE
    _new record;
BEGIN
    _new := new;
    _new."updated_at" = NOW();
    RETURN _new;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER "set_public_vehicle_updated_at"
    BEFORE UPDATE
    ON "public"."vehicle"
    FOR EACH ROW
EXECUTE PROCEDURE "public"."set_current_timestamp_updated_at"();
COMMENT ON TRIGGER "set_public_vehicle_updated_at" ON "public"."vehicle" IS 'trigger to set value of column "updated_at" to current timestamp on row update';

ALTER TABLE "public"."vehicle"
    ADD CONSTRAINT "vehicle_manufacturer_id_fkey" FOREIGN KEY ("manufacturer_id") REFERENCES "public"."manufacturer" ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE "public"."vehicle"
    ALTER COLUMN "id" SET DEFAULT gen_random_uuid();

---

UPDATE public.vehicle
SET created_at = cb.created_at,
    updated_at = cb.created_at
FROM public.created_by cb
WHERE cb.id = vehicle.id;

SELECT audit.audit_table('public.vehicle');

--- Part

ALTER TABLE "public"."part"
    ALTER COLUMN "id" SET DEFAULT gen_random_uuid();

COMMENT ON COLUMN "public"."part"."number" IS NULL;

ALTER TABLE "public"."part"
    ADD CONSTRAINT "part_manufacturer_id_fkey" FOREIGN KEY ("manufacturer_id") REFERENCES "public"."manufacturer" ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

COMMENT ON COLUMN "public"."part"."unit" IS NULL;

CREATE TABLE "public"."unit"
(
    "id"   text NOT NULL,
    "name" text NOT NULL,
    PRIMARY KEY ("id")
);

ALTER TABLE "public"."part"
    ADD COLUMN "updated_at" timestamptz NOT NULL DEFAULT NOW();

CREATE OR REPLACE FUNCTION "public"."set_current_timestamp_updated_at"() RETURNS TRIGGER AS
$$
DECLARE
    _new record;
BEGIN
    _new := new;
    _new."updated_at" = NOW();
    RETURN _new;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER "set_public_part_updated_at"
    BEFORE UPDATE
    ON "public"."part"
    FOR EACH ROW
EXECUTE PROCEDURE "public"."set_current_timestamp_updated_at"();
COMMENT ON TRIGGER "set_public_part_updated_at" ON "public"."part" IS 'trigger to set value of column "updated_at" to current timestamp on row update';


ALTER TABLE "public"."part"
    ADD COLUMN "created_at" timestamptz NOT NULL DEFAULT NOW();

ALTER TABLE "public"."part"
    ADD COLUMN "comment" text NULL;

---

UPDATE public.part
SET created_at = cb.created_at,
    updated_at = cb.created_at
FROM public.created_by cb
WHERE cb.id = part.id;

INSERT INTO "public"."unit"("id", "name")
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

ALTER TABLE "public"."part"
    ADD CONSTRAINT "part_unit_fkey" FOREIGN KEY ("unit") REFERENCES "public"."unit" ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

SELECT audit.audit_table('public.part');
SELECT audit.audit_table('public.unit');

--- Tenant

ALTER TABLE "public"."tenant"
    ADD CONSTRAINT "tenant_group_id_fkey" FOREIGN KEY ("group_id") REFERENCES "public"."tenant_group" ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE "public"."tenant"
    ADD COLUMN "created_at" timestamptz NOT NULL DEFAULT NOW();

ALTER TABLE "public"."tenant"
    ADD COLUMN "updated_at" timestamptz NOT NULL DEFAULT NOW();

CREATE TRIGGER "set_public_tenant_updated_at"
    BEFORE UPDATE
    ON "public"."tenant"
    FOR EACH ROW
EXECUTE PROCEDURE "public"."set_current_timestamp_updated_at"();
COMMENT ON TRIGGER "set_public_tenant_updated_at" ON "public"."tenant" IS 'trigger to set value of column "updated_at" to current timestamp on row update';

ALTER TABLE "public"."tenant"
    ALTER COLUMN "id" SET DEFAULT gen_random_uuid();

ALTER TABLE "public"."tenant_group"
    ADD COLUMN "created_at" timestamptz NOT NULL DEFAULT NOW();

ALTER TABLE "public"."tenant_group"
    ADD COLUMN "updated_at" timestamptz NOT NULL DEFAULT NOW();

CREATE TRIGGER "set_public_tenant_group_updated_at"
    BEFORE UPDATE
    ON "public"."tenant_group"
    FOR EACH ROW
EXECUTE PROCEDURE "public"."set_current_timestamp_updated_at"();
COMMENT ON TRIGGER "set_public_tenant_group_updated_at" ON "public"."tenant_group" IS 'trigger to set value of column "updated_at" to current timestamp on row update';

ALTER TABLE "public"."tenant_group"
    ALTER COLUMN "id" SET DEFAULT gen_random_uuid();

ALTER TABLE "public"."user_permission"
    RENAME TO "tenant_permission";

BEGIN TRANSACTION;
ALTER TABLE "public"."tenant_permission"
    DROP CONSTRAINT "user_permission_pkey";

ALTER TABLE "public"."tenant_permission"
    ADD CONSTRAINT "user_permission_pkey" PRIMARY KEY ("user_id", "tenant_id");
COMMIT TRANSACTION;

ALTER TABLE "public"."tenant_permission"
    DROP COLUMN "id" CASCADE;

BEGIN TRANSACTION;
ALTER TABLE "public"."tenant"
    DROP CONSTRAINT "tenant_pkey";

ALTER TABLE "public"."tenant"
    ADD CONSTRAINT "tenant_pkey" PRIMARY KEY ("id");
COMMIT TRANSACTION;

ALTER TABLE "public"."tenant_permission"
    ADD CONSTRAINT "tenant_permission_tenant_id_fkey" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenant" ("id") ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE "public"."tenant"
    RENAME COLUMN "display_name" TO "name";

ALTER TABLE "public"."tenant_group"
    ADD COLUMN "name" text NOT NULL DEFAULT '';

---

UPDATE public.tenant_group
SET name = CASE WHEN identifier = 'demo' THEN 'Демо'
                WHEN identifier = 'automagistre' THEN 'Автомагистр'
                WHEN identifier = 'shavlev' THEN 'Щавлев В.А.' END;

SELECT audit.audit_table('public.tenant');
SELECT audit.audit_table('public.tenant_group');
SELECT audit.audit_table('public.tenant_permission');
