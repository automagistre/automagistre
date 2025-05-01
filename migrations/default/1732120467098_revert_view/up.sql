DROP TRIGGER motion_part_view_sync ON public.motion;
DROP TRIGGER note_part_view_sync ON public.note;
DROP TRIGGER order_item_part_order_id_sync_trigger ON public.order_item_part;
DROP TRIGGER part_case_part_view_sync ON public.part_case;
DROP TRIGGER part_cross_part_part_view_sync ON public.part_cross_part;
DROP TRIGGER part_discount_part_view_sync ON public.part_discount;
DROP TRIGGER part_view_income_part_sync ON public.income_part;
DROP TRIGGER part_view_manufacturer_sync ON public.manufacturer;
DROP TRIGGER part_view_order_close_sync_trigger ON public.order_close;
DROP TRIGGER part_view_order_item_part_sync ON public.order_item_part;
DROP TRIGGER part_view_part_price_sync ON public.part_price;
DROP TRIGGER part_view_part_required_availability_sync ON public.part_required_availability;
DROP TRIGGER part_view_part_supply_sync ON public.part_supply;
DROP TRIGGER part_view_part_sync ON public.part;
DROP TRIGGER part_view_reservation_sync ON public.reservation;

---

DROP FUNCTION public.note_part_view_sync();
DROP FUNCTION public.note_part_view_sync_trigger();
DROP FUNCTION public.order_item_part_order_id_sync_trigger();
DROP FUNCTION public.part_cross_part_part_view_sync();
DROP FUNCTION public.part_view_income_part_sync_trigger();
DROP FUNCTION public.part_view_initial(part public.part);
DROP FUNCTION public.part_view_manufacturer_sync(m public.manufacturer);
DROP FUNCTION public.part_view_manufacturer_sync_trigger();
DROP FUNCTION public.part_view_motion_sync(_part_id uuid, _tenant_id uuid);
DROP FUNCTION public.part_view_motion_sync_trigger();
DROP FUNCTION public.part_view_order_close_sync_trigger();
DROP FUNCTION public.part_view_order_item_part_sync(_part_id uuid, _tenant_id uuid);
DROP FUNCTION public.part_view_order_item_part_sync_trigger();
DROP FUNCTION public.part_view_part_case_sync_trigger();
DROP FUNCTION public.part_view_part_cross_part_sync_trigger();
DROP FUNCTION public.part_view_part_discount_sync_trigger();
DROP FUNCTION public.part_view_part_price_sync_trigger();
DROP FUNCTION public.part_view_part_required_availability_sync_trigger();
DROP FUNCTION public.part_view_part_supply_sync(_part_id uuid, _tenant_id uuid);
DROP FUNCTION public.part_view_part_supply_sync_trigger();
DROP FUNCTION public.part_view_part_sync_trigger();
DROP FUNCTION public.part_view_reservation_sync(_part_id uuid, _tenant_id uuid);
DROP FUNCTION public.part_view_reservation_sync_trigger();
DROP FUNCTION public.part_view_search_update(_id uuid);

DROP TABLE public.part_view;

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
