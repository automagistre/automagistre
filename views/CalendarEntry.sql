SELECT e.id,
       ces.date         AS schedule_date,
       ces.duration     AS schedule_duration,
       ceoi.customer_id AS order_info_customer_id,
       ceoi.car_id      AS order_info_car_id,
       ceoi.description AS order_info_description,
       ceoi.worker_id   AS order_info_worker_id,
       ceo.order_id     AS order_id
FROM calendar_entry e
         LEFT JOIN calendar_entry_deletion ced ON e.id = ced.entry_id
         LEFT JOIN calendar_entry_order ceo ON ceo.entry_id = e.id
         JOIN LATERAL (SELECT *
                       FROM calendar_entry_schedule sub
                       WHERE sub.entry_id = e.id
                       ORDER BY sub.id DESC
                       LIMIT 1
    ) ces ON TRUE
         JOIN LATERAL (SELECT *
                       FROM calendar_entry_order_info sub
                       WHERE sub.entry_id = e.id
                       ORDER BY sub.id DESC
                       LIMIT 1
    ) ceoi ON TRUE
WHERE ced IS NULL
