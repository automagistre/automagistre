DROP VIEW public.part_view;

--- new table

CREATE TABLE public.part_view
(
    id                          uuid NOT NULL,
    tenant_id                   uuid NOT NULL,
    name                        varchar(255),
    number                      varchar(30),
    is_universal                bool,
    unit                        smallint,
    warehouse_id                uuid,
    quantity                    bigint  DEFAULT 0,
    ordered                     bigint  DEFAULT 0,
    reserved                    bigint  DEFAULT 0,
    analogs                     json    DEFAULT '[]'::json,
    notes                       json    DEFAULT '[]'::json,
    manufacturer_name           varchar(64),
    manufacturer_id             uuid,
    manufacturer_localized_name varchar(255),
    cases                       text,
    search                      text,
    price                       bigint  DEFAULT 0,
    discount                    bigint  DEFAULT 0,
    income                      bigint  DEFAULT 0,
    order_from_quantity         integer DEFAULT 0,
    order_up_to_quantity        integer DEFAULT 0,
    supplies                    json    DEFAULT '[]'::json,
    supplies_quantity           numeric DEFAULT 0,
    UNIQUE (id, tenant_id)
);

--- legacy public.part_view_insert

CREATE FUNCTION public.part_view_initial(part public.part) RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    INSERT INTO public.part_view (id, tenant_id, manufacturer_id, name, number, is_universal, unit, warehouse_id)
    SELECT part.id,
           tenant.id,
           part.manufacturer_id,
           part.name,
           part.number,
           part.universal,
           part.unit,
           part.warehouse_id
    FROM tenant
    ON CONFLICT (id, tenant_id) DO NOTHING;
END ;
$$;

CREATE FUNCTION public.part_view_search_update(_id uuid) RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    WITH pc AS (SELECT pc.cases, UPPER(CONCAT_WS(' '::text, part.name, m.name, m.localized_name, pc.cases)) AS search
                FROM part
                         JOIN public.manufacturer m ON part.manufacturer_id = m.id
                         LEFT JOIN (SELECT part_case.part_id,
                                           ARRAY_TO_STRING(ARRAY_AGG(vm.case_name), ' '::text) AS cases
                                    FROM (public.part_case
                                        LEFT JOIN public.vehicle_model vm
                                          ON ((vm.id = part_case.vehicle_id)))
                                    GROUP BY part_case.part_id) pc ON pc.part_id = part.id
                WHERE part.id = _id)
    UPDATE public.part_view
    SET search = pc.search,
        cases  = pc.cases
    FROM pc
    WHERE part_view.id = _id;
END ;
$$;

--- Sync part

CREATE FUNCTION public.part_view_part_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    IF (tg_op = 'INSERT') THEN
        PERFORM public.part_view_initial(new);

        UPDATE public.part_view AS p
        SET manufacturer_id             = new.manufacturer_id,
            manufacturer_name           = m.name,
            manufacturer_localized_name = m.localized_name,
            name                        = new.name,
            number                      = new.number,
            is_universal                = new.universal,
            unit                        = new.unit,
            warehouse_id                = new.warehouse_id
        FROM public.manufacturer m
        WHERE p.id = new.id
          AND m.id = new.manufacturer_id;

        PERFORM public.part_view_search_update(new.id);
    ELSEIF (tg_op = 'UPDATE') THEN
        UPDATE public.part_view
        SET manufacturer_id = new.manufacturer_id,
            name            = new.name,
            number          = new.number,
            is_universal    = new.universal,
            unit            = new.unit,
            warehouse_id    = new.warehouse_id
        WHERE id = new.id;

        PERFORM public.part_view_search_update(new.id);
    ELSEIF (tg_op = 'DELETE') THEN
        DELETE FROM public.part_view WHERE id = new.id;

        RETURN old;
    END IF;

    RETURN new;
END;
$$;

CREATE TRIGGER part_view_part_sync
    AFTER INSERT OR UPDATE OR DELETE
    ON public.part
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_part_sync_trigger();

--- Sync manufacturer

CREATE FUNCTION public.part_view_manufacturer_sync(m public.manufacturer) RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    UPDATE public.part_view
    SET manufacturer_name           = m.name,
        manufacturer_localized_name = m.localized_name
    WHERE part_view.manufacturer_id = m.id;
END ;
$$;

CREATE FUNCTION public.part_view_manufacturer_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.part_view_manufacturer_sync(new);

    SELECT public.part_view_search_update(part.id) FROM part WHERE manufacturer_id = new.id;

    RETURN new;
END;
$$;

CREATE TRIGGER part_view_manufacturer_sync
    AFTER UPDATE
    ON public.manufacturer
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_manufacturer_sync_trigger();

--- Sync part_case

CREATE FUNCTION public.part_view_part_case_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    IF (tg_op = 'INSERT') THEN
        PERFORM public.part_view_search_update(new.part_id);
    ELSEIF (tg_op = 'UPDATE') THEN
        PERFORM public.part_view_search_update(new.part_id);

        IF new.part_id <> old.part_id THEN
            PERFORM public.part_view_search_update(old.part_id);
        END IF;
    ELSEIF (tg_op = 'DELETE') THEN
        PERFORM public.part_view_search_update(old.part_id);

        RETURN old;
    END IF;

    RETURN new;
END;
$$;

CREATE TRIGGER part_case_part_view_sync
    AFTER INSERT OR UPDATE OR DELETE
    ON public.part_case
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_part_case_sync_trigger();

--- Sync stock

CREATE FUNCTION public.part_view_motion_sync() RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    UPDATE public.part_view
    SET quantity = sub.quantity
    FROM (SELECT motion.part_id,
                 motion.tenant_id,
                 SUM(motion.quantity) AS quantity
          FROM public.motion
          GROUP BY motion.part_id, motion.tenant_id) sub
    WHERE part_view.id = sub.part_id
      AND part_view.tenant_id = sub.tenant_id;
END ;
$$;

CREATE FUNCTION public.part_view_motion_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.part_view_motion_sync();

    RETURN new;
END;
$$;

CREATE TRIGGER motion_part_view_sync
    AFTER INSERT
    ON public.motion
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_motion_sync_trigger();

--- Sync ordered

CREATE FUNCTION public.part_view_order_item_part_sync() RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    UPDATE public.part_view
    SET ordered = COALESCE(sub.quantity, (0)::bigint)
    FROM (SELECT order_item_part.part_id,
                 order_item.tenant_id,
                 SUM(order_item_part.quantity) AS quantity
          FROM ((public.order_item_part JOIN public.order_item
                 ON ((order_item.id = order_item_part.id)))
              LEFT JOIN public.order_close
                ON ((order_item.order_id = order_close.order_id)))
          WHERE (order_close.id IS NULL)
          GROUP BY order_item_part.part_id, order_item.tenant_id) sub
    WHERE part_view.id = sub.part_id
      AND part_view.tenant_id = sub.tenant_id;
END ;
$$;

CREATE FUNCTION public.part_view_order_item_part_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.part_view_order_item_part_sync();

    RETURN new;
END;
$$;

CREATE TRIGGER part_view_order_item_part_sync
    AFTER INSERT OR UPDATE OF quantity
    ON public.order_item_part
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_order_item_part_sync_trigger();

--- Sync reserved

CREATE FUNCTION public.part_view_reservation_sync() RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    UPDATE public.part_view
    SET reserved = COALESCE(sub.quantity, (0)::bigint)
    FROM (SELECT order_item_part.part_id,
                 reservation.tenant_id,
                 SUM(reservation.quantity) AS quantity
          FROM (public.reservation
              JOIN public.order_item_part
                ON ((order_item_part.id = reservation.order_item_part_id)))
          GROUP BY order_item_part.part_id, reservation.tenant_id) sub
    WHERE part_view.id = sub.part_id
      AND part_view.tenant_id = sub.tenant_id;
END ;
$$;

CREATE FUNCTION public.part_view_reservation_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.part_view_reservation_sync();

    RETURN new;
END;
$$;

CREATE TRIGGER part_view_reservation_sync
    AFTER INSERT OR UPDATE OF quantity
    ON public.reservation
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_reservation_sync_trigger();

--- Sync crosses

CREATE FUNCTION public.part_cross_part_part_view_sync() RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    UPDATE public.part_view
    SET analogs = COALESCE(sub.parts, '[]'::json)
    FROM (SELECT pcp.part_id,
                 JSON_AGG(pcp2.part_id) FILTER (WHERE (pcp2.part_id IS NOT NULL)) AS parts
          FROM (public.part_cross_part pcp
              LEFT JOIN public.part_cross_part pcp2
                ON ((
                    (pcp2.part_cross_id = pcp.part_cross_id) AND
                    (pcp2.part_id <> pcp.part_id))))
          GROUP BY pcp.part_id) sub
    WHERE part_view.id = sub.part_id;
END ;
$$;

CREATE FUNCTION public.part_view_part_cross_part_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.part_cross_part_part_view_sync();

    RETURN new;
END;
$$;

CREATE TRIGGER part_cross_part_part_view_sync
    AFTER INSERT OR UPDATE OR DELETE
    ON public.part_cross_part
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_part_cross_part_sync_trigger();

--- Sync notes

CREATE FUNCTION public.note_part_view_sync() RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    UPDATE public.part_view
    SET notes = COALESCE(sub.json, '[]'::json)
    FROM (SELECT note.subject,
                 note.tenant_id,
                 JSON_AGG(JSON_BUILD_OBJECT('type', note.type, 'text', note.text)) AS json
          FROM public.note
                   LEFT JOIN public.note_delete del ON del.note_id = note.id
          WHERE del.id IS NULL
          GROUP BY note.subject, note.tenant_id) sub
    WHERE part_view.tenant_id = sub.tenant_id
      AND part_view.id = sub.subject;
END ;
$$;

CREATE FUNCTION public.note_part_view_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.note_part_view_sync();

    RETURN new;
END;
$$;

CREATE TRIGGER note_part_view_sync
    AFTER INSERT OR UPDATE OR DELETE
    ON public.note
    FOR EACH ROW
EXECUTE PROCEDURE public.note_part_view_sync_trigger();

--- Sync price

CREATE FUNCTION public.part_view_part_price_sync() RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    UPDATE public.part_view
    SET price = COALESCE(sub.price_amount, (0)::bigint)
    FROM (SELECT ROW_NUMBER()
                 OVER (PARTITION BY pp.part_id, pp.tenant_id ORDER BY pp.id DESC) AS rownum,
                 pp.id,
                 pp.part_id,
                 pp.since,
                 pp.tenant_id,
                 pp.price_amount,
                 pp.price_currency_code
          FROM public.part_price pp) sub
    WHERE part_view.id = sub.part_id
      AND sub.rownum = 1;
END ;
$$;

CREATE FUNCTION public.part_view_part_price_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.part_view_part_price_sync();

    RETURN new;
END;
$$;

CREATE TRIGGER part_view_part_price_sync
    AFTER INSERT OR UPDATE
    ON public.part_price
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_part_price_sync_trigger();

--- Sync discount

CREATE FUNCTION public.part_view_part_discount_sync() RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    UPDATE public.part_view
    SET discount = COALESCE(sub.discount_amount, (0)::bigint)
    FROM (SELECT ROW_NUMBER()
                 OVER (PARTITION BY pd.part_id, pd.tenant_id ORDER BY pd.id DESC) AS rownum,
                 pd.id,
                 pd.part_id,
                 pd.since,
                 pd.tenant_id,
                 pd.discount_amount,
                 pd.discount_currency_code
          FROM public.part_discount pd) sub
    WHERE part_view.id = sub.part_id
      AND sub.rownum = 1;

END ;
$$;

CREATE FUNCTION public.part_view_part_discount_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.part_view_part_discount_sync();

    RETURN new;
END;
$$;

CREATE TRIGGER part_discount_part_view_sync
    AFTER INSERT
    ON public.part_discount
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_part_discount_sync_trigger();

--- Sync income

CREATE FUNCTION public.part_view_income_part_sync() RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    UPDATE public.part_view
    SET income = COALESCE(sub.price_amount, (0)::bigint)
    FROM (SELECT ROW_NUMBER()
                 OVER (PARTITION BY income_part.part_id, income_part.tenant_id ORDER BY income_part.id DESC) AS rownum,
                 income_part.id,
                 income_part.income_id,
                 income_part.part_id,
                 income_part.quantity,
                 income_part.tenant_id,
                 income_part.price_amount,
                 income_part.price_currency_code
          FROM public.income_part) sub
    WHERE part_view.id = sub.part_id
      AND sub.rownum = 1;
END ;
$$;

CREATE FUNCTION public.part_view_income_part_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.part_view_income_part_sync();

    RETURN new;
END;
$$;

CREATE TRIGGER part_view_income_part_sync
    AFTER INSERT
    ON public.income_part
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_income_part_sync_trigger();

--- Sync part_required_availability

CREATE FUNCTION public.part_view_part_required_availability_sync() RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    UPDATE public.part_view
    SET order_from_quantity  = COALESCE(sub.order_from_quantity, 0),
        order_up_to_quantity = COALESCE(sub.order_up_to_quantity, 0)
    FROM (SELECT ROW_NUMBER()
                 OVER (PARTITION BY pra.part_id, pra.tenant_id ORDER BY pra.id DESC) AS rownum,
                 pra.id,
                 pra.part_id,
                 pra.order_from_quantity,
                 pra.order_up_to_quantity,
                 pra.tenant_id
          FROM public.part_required_availability pra) sub
    WHERE part_view.id = sub.part_id
      AND sub.rownum = 1;
END ;
$$;

CREATE FUNCTION public.part_view_part_required_availability_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.part_view_part_required_availability_sync();

    RETURN new;
END;
$$;

CREATE TRIGGER part_view_part_required_availability_sync
    AFTER INSERT
    ON public.part_required_availability
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_part_required_availability_sync_trigger();

--- Sync part_supply

CREATE OR REPLACE FUNCTION public.part_view_part_supply_sync(_part_id uuid, _tenant_id uuid) RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    UPDATE public.part_view
    SET supplies          = COALESCE(sub.json, '[]'::json),
        supplies_quantity = COALESCE(sub.quantity, (0)::numeric)
    FROM (SELECT JSON_AGG(JSON_BUILD_OBJECT(
            'supplier_id', sub.supplier_id,
            'quantity', sub.quantity,
            'updatedAt', sub.updated_at
                          ))       AS json,
                 SUM(sub.quantity) AS quantity
          FROM (SELECT part_supply.supplier_id,
                       SUM(part_supply.quantity)   AS quantity,
                       MAX(part_supply.created_at) AS updated_at
                FROM public.part_supply
                WHERE part_supply.part_id = _part_id
                  AND part_supply.tenant_id = _tenant_id
                GROUP BY part_supply.supplier_id
                HAVING SUM(part_supply.quantity) > 0) sub) sub
    WHERE part_view.tenant_id = _tenant_id
      AND part_view.id = _part_id;
END ;
$$;

CREATE OR REPLACE FUNCTION public.part_view_part_supply_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
DECLARE
    _part_id   uuid;
    _tenant_id uuid;
BEGIN
    IF (tg_op = 'INSERT') OR (tg_op = 'UPDATE') THEN
        _part_id = new.part_id;
        _tenant_id = new.tenant_id;
    ELSEIF (tg_op = 'DELETE') THEN
        _part_id = old.part_id;
        _tenant_id = old.tenant_id;
    END IF;

    PERFORM public.part_view_part_supply_sync(_part_id, _tenant_id);

    RETURN new;
END;
$$;

CREATE TRIGGER part_view_part_supply_sync
    AFTER INSERT OR UPDATE OF quantity OR DELETE
    ON public.part_supply
    FOR EACH ROW
EXECUTE PROCEDURE public.part_view_part_supply_sync_trigger();


--- initial fill part_view

SELECT public.part_view_initial(part)
FROM part;

SELECT public.part_view_manufacturer_sync(m)
FROM public.manufacturer m;

SELECT public.part_view_search_update(id)
FROM part;

SELECT public.part_view_motion_sync();

SELECT public.part_view_order_item_part_sync();

SELECT public.part_view_reservation_sync();

SELECT public.part_cross_part_part_view_sync();

SELECT public.note_part_view_sync();

SELECT public.part_view_part_price_sync();

SELECT public.part_view_part_discount_sync();

SELECT public.part_view_income_part_sync();

SELECT public.part_view_part_required_availability_sync();

SELECT public.part_view_part_supply_sync();

---

CREATE INDEX customer_transaction_operand_id_tenant_id_idx ON customer_transaction (operand_id, tenant_id);
CREATE INDEX order_item_part_part_id_idx ON order_item_part (part_id);

--- Add wallet.balance

ALTER TABLE public.wallet
    ADD COLUMN balance numeric DEFAULT 0 NOT NULL;

CREATE FUNCTION public.wallet_balance_sync(_id uuid) RETURNS void
    LANGUAGE sql AS
$$
UPDATE wallet
SET balance = (SELECT COALESCE(SUM(wt.amount_amount), (0)::numeric)
               FROM public.wallet_transaction wt
               WHERE wt.wallet_id = _id
               GROUP BY wt.wallet_id)
WHERE id = _id;
$$;

CREATE FUNCTION public.wallet_balance_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.wallet_balance_sync(new.wallet_id);

    RETURN new;
END;
$$;

CREATE TRIGGER wallet_balance_sync_trigger
    AFTER INSERT
    ON public.wallet_transaction
    FOR EACH ROW
EXECUTE PROCEDURE public.wallet_balance_sync_trigger();

SELECT public.wallet_balance_sync(id)
FROM public.wallet;

--- recreate wallet_view for BC
CREATE VIEW public.wallet_view AS
SELECT w.id,
       w.tenant_id,
       w.name,
       w.currency_code,
       w.default_in_manual_transaction,
       w.show_in_layout,
       w.use_in_income,
       w.use_in_order,
       w.balance
FROM public.wallet w;

--- organization.balance

ALTER TABLE public.organization
    ADD COLUMN balance numeric DEFAULT 0 NOT NULL;

CREATE FUNCTION public.organization_balance_sync(_id uuid) RETURNS void
    LANGUAGE sql AS
$$
UPDATE organization
SET balance = COALESCE((SELECT SUM(ct.amount_amount)
                        FROM public.customer_transaction ct
                        WHERE ct.operand_id = _id
                        GROUP BY ct.operand_id), 0)
WHERE id = _id;
$$;

CREATE FUNCTION public.organization_balance_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.organization_balance_sync(new.operand_id);

    RETURN new;
END;
$$;

CREATE TRIGGER organization_balance_sync_trigger
    AFTER INSERT
    ON public.customer_transaction
    FOR EACH ROW
EXECUTE PROCEDURE public.organization_balance_sync_trigger();

SELECT public.organization_balance_sync(id)
FROM public.organization;

--- person.balance

ALTER TABLE public.person
    ADD COLUMN balance numeric DEFAULT 0 NOT NULL;

CREATE FUNCTION public.person_balance_sync(_id uuid) RETURNS void
    LANGUAGE sql AS
$$
UPDATE person
SET balance = COALESCE((SELECT SUM(ct.amount_amount)
                        FROM public.customer_transaction ct
                        WHERE ct.operand_id = _id
                        GROUP BY ct.operand_id), 0)
WHERE id = _id;
$$;

CREATE FUNCTION public.person_balance_sync_trigger() RETURNS trigger
    LANGUAGE plpgsql
AS
$$
BEGIN
    PERFORM public.person_balance_sync(new.operand_id);

    RETURN new;
END;
$$;

CREATE TRIGGER person_balance_sync_trigger
    AFTER INSERT
    ON public.customer_transaction
    FOR EACH ROW
EXECUTE PROCEDURE public.person_balance_sync_trigger();

SELECT public.person_balance_sync(id)
FROM public.person;

--- recreate customer_view with balance

CREATE OR REPLACE VIEW public.customer_view
AS
SELECT o.id,
       o.tenant_group_id,
       o.name               AS full_name,
       o.balance,
       o.email,
       o.telephone,
       o.office_phone,
       o.seller,
       o.contractor,
       o.address,
       'organization'::text AS type
FROM public.organization o
UNION ALL
SELECT p.id,
       p.tenant_group_id,
       CONCAT_WS(' '::text, p.lastname, p.firstname) AS full_name,
       p.balance,
       p.email,
       p.telephone,
       p.office_phone,
       p.seller,
       p.contractor,
       NULL::character varying                       AS address,
       'person'::text                                AS type
FROM public.person p;
