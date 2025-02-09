CREATE OR REPLACE VIEW public.appeal_view(id, tenant_id, name, type, phone, email, status, created_at) AS
SELECT appeal.id,
       appeal.tenant_id,
       appeal.name,
       appeal.type,
       appeal.phone,
       appeal.email,
       COALESCE(status.status::integer, 1) AS status,
       created_by.created_at
FROM (SELECT appeal_calculator.id,
             appeal_calculator.name,
             1          AS type,
             appeal_calculator.phone,
             NULL::text AS email,
             appeal_calculator.tenant_id
      FROM appeal_calculator
      UNION ALL
      SELECT appeal_cooperation.id,
             appeal_cooperation.name,
             2          AS type,
             appeal_cooperation.phone,
             NULL::text AS email,
             appeal_cooperation.tenant_id
      FROM appeal_cooperation
      UNION ALL
      SELECT appeal_question.id,
             appeal_question.name,
             3                       AS type,
             NULL::character varying AS phone,
             appeal_question.email,
             appeal_question.tenant_id
      FROM appeal_question
      UNION ALL
      SELECT appeal_schedule.id,
             appeal_schedule.name,
             4           AS type,
             appeal_schedule.phone,
             NULL ::text AS email,
             appeal_schedule.tenant_id
      FROM appeal_schedule
      UNION ALL
      SELECT appeal_tire_fitting.id,
             appeal_tire_fitting.name,
             5           AS type,
             appeal_tire_fitting.phone,
             NULL ::text AS email,
             appeal_tire_fitting.tenant_id
      FROM appeal_tire_fitting
      UNION ALL
      SELECT appeal_call.id,
             '':: character varying AS "varchar",
             6                      AS type,
             appeal_call.phone,
             NULL ::text            AS email,
             appeal_call.tenant_id
      FROM appeal_call) appeal
         LEFT JOIN LATERAL ( SELECT sub.id,
                                    sub.appeal_id,
                                    sub.status,
                                    sub.tenant_id
                             FROM appeal_status sub
                             WHERE sub.appeal_id = appeal.id
                             ORDER BY sub.id DESC
                             LIMIT 1) status ON TRUE
         JOIN created_by ON created_by.id = appeal.id;

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
       cb.created_at,
       cb.user_id       AS created_by
FROM customer_transaction ct
         JOIN created_by cb ON cb.id = ct.id
         LEFT JOIN wallet_transaction wt ON wt.id = ct.source_id;

---

CREATE OR REPLACE VIEW public.inventorization_view(id, tenant_id, created_at, closed_at) AS
SELECT i.id,
       i.tenant_id,
       cb.created_at,
       cbc.created_at AS closed_at
FROM inventorization i
         JOIN created_by cb ON cb.id = i.id
         LEFT JOIN inventorization_close ic ON i.id = ic.inventorization_id
         LEFT JOIN created_by cbc ON cbc.id = ic.id;

---

CREATE OR REPLACE VIEW public.note_view(id, tenant_id, subject, text, type, created_at, created_by) AS
SELECT note.id,
       note.tenant_id,
       note.subject,
       note.text,
       note.type,
       cb.created_at,
       cb.user_id AS created_by
FROM note
         JOIN created_by cb ON cb.id = note.id
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
                    WHERE order_close.* IS NULL
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
                                 SUM(part_supply.quantity)  AS quantity,
                                 MAX(created_by.created_at) AS updated_at
                          FROM part_supply
                                   LEFT JOIN created_by ON created_by.id = part_supply.id
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
       cb.user_id  AS created_by,
       cb.created_at
FROM (SELECT ROW_NUMBER() OVER (PARTITION BY publish.entity_id ORDER BY publish.id DESC) AS rownum,
             publish.id,
             publish.entity_id,
             publish.published,
             publish.tenant_id
      FROM publish) p
         JOIN created_by cb ON cb.id = p.id
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
       es_cb.user_id     AS created_by,
       es_cb.created_at,
       ese_cb.user_id    AS ended_by,
       ese_cb.created_at AS ended_at
FROM employee_salary es
         JOIN created_by es_cb ON es_cb.id = es.id
         JOIN employee ON employee.id = es.employee_id
         LEFT JOIN employee_salary_end ese ON es.id = ese.salary_id
         LEFT JOIN created_by ese_cb ON ese_cb.id = ese.id;

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
       cb.created_at,
       cb.user_id       AS created_by
FROM wallet_transaction wt
         JOIN created_by cb ON cb.id = wt.id
         LEFT JOIN customer_transaction ct ON ct.id = wt.source_id;
