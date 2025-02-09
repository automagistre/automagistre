--- Migrate created_at and created_by to table

CREATE OR REPLACE FUNCTION public.add_created_by(tbl regclass, pk text DEFAULT 'id') RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    EXECUTE FORMAT(
            'ALTER TABLE %s ADD COLUMN IF NOT EXISTS created_by uuid DEFAULT current_setting(''app.user_id'')::uuid',
            tbl);
    EXECUTE FORMAT(
            'UPDATE %1$s SET created_by = (SELECT created_by.user_id FROM created_by WHERE %1$s.%2$s = created_by.%2$s);',
            tbl, pk);
END ;
$$;

CREATE OR REPLACE FUNCTION public.add_created_at(tbl regclass, pk text DEFAULT 'id') RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    EXECUTE FORMAT('ALTER TABLE %s ADD COLUMN IF NOT EXISTS created_at timestamp(0) WITH TIME ZONE DEFAULT NOW()', tbl);
    EXECUTE FORMAT(
            'UPDATE %1$s SET created_at = ' ||
            '(SELECT created_by.created_at FROM created_by WHERE %1$s.%2$s = created_by.%2$s);',
            tbl, pk);
END ;
$$;

CREATE OR REPLACE FUNCTION public.add_created_by_all(tbl regclass, pk text DEFAULT 'id') RETURNS void
    LANGUAGE plpgsql AS
$$
BEGIN
    PERFORM public.add_created_at(tbl, pk);
    PERFORM public.add_created_by(tbl, pk);
END ;
$$;

SET app.user_id = '00000000-0000-0000-0000-000000000000';
SELECT public.add_created_by_all('appeal_calculator');
SELECT public.add_created_by_all('appeal_call');
SELECT public.add_created_by_all('appeal_cooperation');
SELECT public.add_created_by_all('appeal_postpone');
SELECT public.add_created_by_all('appeal_question');
SELECT public.add_created_by_all('appeal_schedule');
SELECT public.add_created_by_all('appeal_status');
SELECT public.add_created_by_all('appeal_tire_fitting');
SELECT public.add_created_by_all('calendar_entry');
SELECT public.add_created_by_all('calendar_entry_deletion');
SELECT public.add_created_by_all('calendar_entry_order');
SELECT public.add_created_by_all('calendar_entry_order_info');
SELECT public.add_created_by_all('calendar_entry_schedule');
SELECT public.add_created_by_all('car');
SELECT public.add_created_by_all('car_recommendation');
SELECT public.add_created_by_all('car_recommendation_part');
SELECT public.add_created_by_all('customer_transaction');
SELECT public.add_created_by_all('employee');
SELECT public.add_created_by_all('employee_salary');
SELECT public.add_created_by_all('employee_salary_end');
SELECT public.add_created_by_all('expense');
SELECT public.add_created_by_all('google_review_token');
SELECT public.add_created_by_all('income');
SELECT public.add_created_by_all('income_accrue');
SELECT public.add_created_by_all('income_part');
SELECT public.add_created_by_all('inventorization');
SELECT public.add_created_by_all('inventorization_close');
SELECT public.add_created_by('manufacturer');
SELECT public.add_created_by_all('mc_equipment');
SELECT public.add_created_by_all('mc_line');
SELECT public.add_created_by_all('mc_part');
SELECT public.add_created_by_all('mc_work');
SELECT public.add_created_by_all('motion');
SELECT public.add_created_by_all('note');
SELECT public.add_created_by_all('note_delete');
SELECT public.add_created_by_all('order_cancel');
SELECT public.add_created_by_all('order_close');
SELECT public.add_created_by_all('order_deal');
SELECT public.add_created_by_all('order_item_group');
SELECT public.add_created_by_all('order_item_part');
SELECT public.add_created_by_all('order_item_service');
SELECT public.add_created_by_all('order_payment');
SELECT public.add_created_by_all('order_suspend');
SELECT public.add_created_by_all('orders');
SELECT public.add_created_by_all('organization');
SELECT public.add_created_by_all('part');
SELECT public.add_created_by_all('part_case');
SELECT public.add_created_by_all('part_discount');
SELECT public.add_created_by_all('part_price');
SELECT public.add_created_by_all('part_required_availability');
SELECT public.add_created_by_all('part_supply');
SELECT public.add_created_by_all('person');
SELECT public.add_created_by_all('publish');
SELECT public.add_created_by_all('reservation');
SELECT public.add_created_by_all('review');
SELECT public.add_created_by_all('sms');
SELECT public.add_created_by_all('sms_send');
SELECT public.add_created_by_all('sms_status');
SELECT public.add_created_by_all('vehicle_model');
SELECT public.add_created_by_all('wallet');
SELECT public.add_created_by_all('wallet_transaction');
SELECT public.add_created_by_all('warehouse');
SELECT public.add_created_by_all('warehouse_code');
SELECT public.add_created_by_all('warehouse_name');
SELECT public.add_created_by_all('warehouse_parent');

--- clean

DROP FUNCTION public.add_created_by(tbl regclass, pk text);
DROP FUNCTION public.add_created_at(tbl regclass, pk text);
DROP FUNCTION public.add_created_by_all(tbl regclass, pk text);

---

CREATE OR REPLACE VIEW public.appeal_view(id, tenant_id, name, type, phone, email, status, created_at) AS
SELECT appeal.id,
       appeal.tenant_id,
       appeal.name,
       appeal.type,
       appeal.phone,
       appeal.email,
       COALESCE(status.status::integer, 1) AS status,
       appeal.created_at
FROM (SELECT appeal_calculator.id,
             appeal_calculator.name,
             1          AS type,
             appeal_calculator.phone,
             NULL::text AS email,
             appeal_calculator.tenant_id,
             appeal_calculator.created_at
      FROM appeal_calculator
      UNION ALL
      SELECT appeal_cooperation.id,
             appeal_cooperation.name,
             2          AS type,
             appeal_cooperation.phone,
             NULL::text AS email,
             appeal_cooperation.tenant_id,
             appeal_cooperation.created_at
      FROM appeal_cooperation
      UNION ALL
      SELECT appeal_question.id,
             appeal_question.name,
             3                       AS type,
             NULL::character varying AS phone,
             appeal_question.email,
             appeal_question.tenant_id,
             appeal_question.created_at
      FROM appeal_question
      UNION ALL
      SELECT appeal_schedule.id,
             appeal_schedule.name,
             4          AS type,
             appeal_schedule.phone,
             NULL::text AS email,
             appeal_schedule.tenant_id,
             appeal_schedule.created_at
      FROM appeal_schedule
      UNION ALL
      SELECT appeal_tire_fitting.id,
             appeal_tire_fitting.name,
             5          AS type,
             appeal_tire_fitting.phone,
             NULL::text AS email,
             appeal_tire_fitting.tenant_id,
             appeal_tire_fitting.created_at
      FROM appeal_tire_fitting
      UNION ALL
      SELECT appeal_call.id,
             ''::character varying AS "varchar",
             6                     AS type,
             appeal_call.phone,
             NULL::text            AS email,
             appeal_call.tenant_id,
             appeal_call.created_at
      FROM appeal_call) appeal
         LEFT JOIN LATERAL ( SELECT sub.id,
                                    sub.appeal_id,
                                    sub.status,
                                    sub.tenant_id
                             FROM appeal_status sub
                             WHERE sub.appeal_id = appeal.id
                             ORDER BY sub.id DESC
                             LIMIT 1) status ON TRUE;

---

CREATE OR REPLACE VIEW public.customer_transaction_view
            (id, tenant_id, operand_id, amount, source, source_id, description, created_at, created_by) AS
SELECT ct.id,
       ct.tenant_id,
       ct.operand_id,
       ct.amount_amount AS amount,
       ct.source,
       CASE
           WHEN ct.source = ANY (ARRAY [5, 10]) THEN wt.wallet_id
           ELSE ct.source_id
           END          AS source_id,
       ct.description,
       ct.created_at,
       ct.created_by
FROM customer_transaction ct
         LEFT JOIN wallet_transaction wt ON wt.id = ct.source_id;

---

CREATE OR REPLACE VIEW public.inventorization_view(id, tenant_id, created_at, closed_at) AS
SELECT i.id,
       i.tenant_id,
       cb.created_at,
       cb.created_at AS closed_at
FROM inventorization i
         JOIN created_by cb ON cb.id = i.id
         LEFT JOIN inventorization_close ic ON i.id = ic.inventorization_id;

---

CREATE OR REPLACE VIEW public.inventorization_view(id, tenant_id, created_at, closed_at) AS
SELECT i.id,
       i.tenant_id,
       cb.created_at,
       cb.created_at AS closed_at
FROM inventorization i
         JOIN created_by cb ON cb.id = i.id
         LEFT JOIN inventorization_close ic ON i.id = ic.inventorization_id;

---

CREATE OR REPLACE VIEW public.note_view(id, tenant_id, subject, text, type, created_at, created_by) AS
SELECT note.id,
       note.tenant_id,
       note.subject,
       note.text,
       note.type,
       note.created_at,
       note.created_by
FROM note
         LEFT JOIN note_delete ON note_delete.note_id = note.id
WHERE note_delete.id IS NULL;

---

CREATE OR REPLACE VIEW public.part_view
            (id, tenant_id, name, number, is_universal, unit, warehouse_id, quantity, ordered, reserved, analogs, notes,
             manufacturer_name, manufacturer_id, manufacturer_localized_name, cases, search, price, discount, income,
             order_from_quantity, order_up_to_quantity, supplies, supplies_quantity)
AS
SELECT part.id,
       tenant.id                                                                  AS tenant_id,
       part.name,
       part.number,
       part.universal                                                             AS is_universal,
       part.unit,
       part.warehouse_id,
       COALESCE(stock.quantity, 0::bigint)                                        AS quantity,
       COALESCE(ordered.quantity, 0::bigint)                                      AS ordered,
       COALESCE(reserved.quantity, 0::bigint)                                     AS reserved,
       COALESCE(crosses.parts, '[]'::json)                                        AS analogs,
       COALESCE(notes.json, '[]'::json)                                           AS notes,
       m.name                                                                     AS manufacturer_name,
       m.id                                                                       AS manufacturer_id,
       m.localized_name                                                           AS manufacturer_localized_name,
       pc.cases,
       UPPER(CONCAT_WS(' '::text, part.name, m.name, m.localized_name, pc.cases)) AS search,
       COALESCE(price.price_amount, 0::bigint)                                    AS price,
       COALESCE(discount.discount_amount, 0::bigint)                              AS discount,
       COALESCE(income.price_amount, 0::bigint)                                   AS income,
       COALESCE(part_required.order_from_quantity, 0)                             AS order_from_quantity,
       COALESCE(part_required.order_up_to_quantity, 0)                            AS order_up_to_quantity,
       COALESCE(supply.json, '[]'::json)                                          AS supplies,
       COALESCE(supply.quantity, 0::numeric)                                      AS supplies_quantity
FROM part
         JOIN tenant ON TRUE
         JOIN manufacturer m ON part.manufacturer_id = m.id
         LEFT JOIN (SELECT part_case.part_id,
                           ARRAY_TO_STRING(ARRAY_AGG(vm.case_name), ' '::text) AS cases
                    FROM part_case
                             LEFT JOIN vehicle_model vm ON vm.id = part_case.vehicle_id
                    GROUP BY part_case.part_id) pc ON pc.part_id = part.id
         LEFT JOIN (SELECT ROW_NUMBER() OVER (PARTITION BY pra.part_id, pra.tenant_id ORDER BY pra.id DESC) AS rownum,
                           pra.id,
                           pra.part_id,
                           pra.order_from_quantity,
                           pra.order_up_to_quantity,
                           pra.tenant_id
                    FROM part_required_availability pra) part_required
                   ON part_required.part_id = part.id AND part_required.rownum = 1 AND
                      tenant.id = part_required.tenant_id
         LEFT JOIN (SELECT ROW_NUMBER() OVER (PARTITION BY pp.part_id, pp.tenant_id ORDER BY pp.id DESC) AS rownum,
                           pp.id,
                           pp.part_id,
                           pp.since,
                           pp.tenant_id,
                           pp.price_amount,
                           pp.price_currency_code
                    FROM part_price pp) price
                   ON price.part_id = part.id AND price.rownum = 1 AND tenant.id = price.tenant_id
         LEFT JOIN (SELECT ROW_NUMBER() OVER (PARTITION BY pd.part_id, pd.tenant_id ORDER BY pd.id DESC) AS rownum,
                           pd.id,
                           pd.part_id,
                           pd.since,
                           pd.tenant_id,
                           pd.discount_amount,
                           pd.discount_currency_code
                    FROM part_discount pd) discount
                   ON discount.part_id = part.id AND discount.rownum = 1 AND tenant.id = discount.tenant_id
         LEFT JOIN (SELECT ROW_NUMBER()
                           OVER (PARTITION BY income_part.part_id, income_part.tenant_id ORDER BY income_part.id DESC) AS rownum,
                           income_part.id,
                           income_part.income_id,
                           income_part.part_id,
                           income_part.quantity,
                           income_part.tenant_id,
                           income_part.price_amount,
                           income_part.price_currency_code
                    FROM income_part) income ON income.part_id = part.id AND income.rownum = 1
         LEFT JOIN (SELECT motion.part_id,
                           motion.tenant_id,
                           SUM(motion.quantity) AS quantity
                    FROM motion
                    GROUP BY motion.part_id, motion.tenant_id) stock
                   ON stock.part_id = part.id AND tenant.id = stock.tenant_id
         LEFT JOIN (SELECT order_item_part.part_id,
                           order_item.tenant_id,
                           SUM(order_item_part.quantity) AS quantity
                    FROM order_item_part
                             JOIN order_item ON order_item.id = order_item_part.id
                             LEFT JOIN order_close ON order_item.order_id = order_close.order_id
                    WHERE order_close.id IS NULL
                    GROUP BY order_item_part.part_id, order_item.tenant_id) ordered
                   ON ordered.part_id = part.id AND tenant.id = ordered.tenant_id
         LEFT JOIN (SELECT order_item_part.part_id,
                           reservation.tenant_id,
                           SUM(reservation.quantity) AS quantity
                    FROM reservation
                             JOIN order_item_part ON order_item_part.id = reservation.order_item_part_id
                    GROUP BY order_item_part.part_id, reservation.tenant_id) reserved
                   ON reserved.part_id = part.id AND tenant.id = reserved.tenant_id
         LEFT JOIN (SELECT JSON_AGG(JSON_BUILD_OBJECT('supplier_id', sub.supplier_id, 'quantity', sub.quantity,
                                                      'updatedAt', sub.updated_at)) AS json,
                           sub.tenant_id,
                           sub.part_id,
                           SUM(sub.quantity)                                        AS quantity
                    FROM (SELECT part_supply.part_id,
                                 part_supply.tenant_id,
                                 part_supply.supplier_id,
                                 SUM(part_supply.quantity)   AS quantity,
                                 MAX(part_supply.created_at) AS updated_at
                          FROM part_supply
                          GROUP BY part_supply.part_id, part_supply.tenant_id, part_supply.supplier_id
                          HAVING SUM(part_supply.quantity) <> 0) sub
                    GROUP BY sub.part_id, sub.tenant_id) supply
                   ON supply.part_id = part.id AND tenant.id = supply.tenant_id
         LEFT JOIN (SELECT pcp.part_id,
                           JSON_AGG(pcp2.part_id) FILTER (WHERE pcp2.part_id IS NOT NULL) AS parts
                    FROM part_cross_part pcp
                             LEFT JOIN part_cross_part pcp2
                                       ON pcp2.part_cross_id = pcp.part_cross_id AND pcp2.part_id <> pcp.part_id
                    GROUP BY pcp.part_id) crosses ON crosses.part_id = part.id
         LEFT JOIN (SELECT note.subject,
                           note.tenant_id,
                           JSON_AGG(JSON_BUILD_OBJECT('type', note.type, 'text', note.text)) AS json
                    FROM note
                    GROUP BY note.subject, note.tenant_id) notes
                   ON notes.subject = part.id AND tenant.id = notes.tenant_id;

---

CREATE OR REPLACE VIEW public.publish_view(id, tenant_id, published, created_by, created_at) AS
SELECT p.entity_id AS id,
       p.tenant_id,
       p.published,
       p.created_by,
       p.created_at
FROM (SELECT ROW_NUMBER() OVER (PARTITION BY publish.entity_id ORDER BY publish.id DESC) AS rownum,
             publish.id,
             publish.entity_id,
             publish.published,
             publish.tenant_id,
             publish.created_by,
             publish.created_at
      FROM publish) p
WHERE p.rownum = 1;

---

CREATE OR REPLACE VIEW public.salary_view
            (id, tenant_id, employee_id, payday, amount, person_id, created_by, created_at, ended_by, ended_at) AS
SELECT es.id,
       es.tenant_id,
       es.employee_id,
       es.payday,
       es.amount,
       employee.person_id,
       es.created_by,
       es.created_at,
       ese.created_by AS ended_by,
       ese.created_at AS ended_at
FROM employee_salary es
         JOIN employee ON employee.id = es.employee_id
         LEFT JOIN employee_salary_end ese ON es.id = ese.salary_id;

---

CREATE OR REPLACE VIEW public.wallet_transaction_view
            (id, tenant_id, wallet_id, amount, source, source_id, description, created_at, created_by) AS
SELECT wt.id,
       wt.tenant_id,
       wt.wallet_id,
       wt.amount_amount AS amount,
       wt.source,
       CASE
           WHEN wt.source = ANY (ARRAY [3, 6]) THEN ct.operand_id
           ELSE wt.source_id
           END          AS source_id,
       wt.description,
       wt.created_at,
       wt.created_by
FROM wallet_transaction wt
         LEFT JOIN customer_transaction ct ON ct.id = wt.source_id;
