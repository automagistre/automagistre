SELECT root.id                AS id,
       wn.name                AS name,
       wp.warehouse_parent_id AS parent_id
FROM warehouse root
         JOIN LATERAL (SELECT name
                       FROM warehouse_name sub
                       WHERE sub.warehouse_id = root.id
                       ORDER BY sub.id DESC
                       LIMIT 1
    ) wn ON TRUE
         LEFT JOIN LATERAL (SELECT warehouse_parent_id
                            FROM warehouse_parent sub
                            WHERE sub.warehouse_id = root.id
                            ORDER BY sub.id DESC
                            LIMIT 1
    ) wp ON TRUE
