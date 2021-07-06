WITH RECURSIVE tree (id) AS (
    SELECT id, wp.warehouse_parent_id AS parent_id, 0 AS depth
    FROM warehouse root
             LEFT JOIN LATERAL (SELECT warehouse_parent_id
                                FROM warehouse_parent sub
                                WHERE sub.warehouse_id = root.id
                                ORDER BY sub.id DESC
                                LIMIT 1
        ) wp ON TRUE
    WHERE wp.warehouse_parent_id IS NULL

    UNION ALL

    SELECT root.id, wp.warehouse_parent_id AS parent_id, p.depth + 1 AS depth
    FROM warehouse root
             LEFT JOIN LATERAL (SELECT warehouse_parent_id
                                FROM warehouse_parent sub
                                WHERE sub.warehouse_id = root.id
                                ORDER BY sub.id DESC
                                LIMIT 1
        ) wp ON TRUE
             JOIN tree p ON p.id = wp.warehouse_parent_id
)
SELECT tree.id        AS id,
       wn.name        AS NAME,
       tree.parent_id AS parent_id,
       tree.depth     AS depth
FROM tree
         JOIN LATERAL (SELECT NAME
                       FROM warehouse_name sub
                       WHERE sub.warehouse_id = tree.id
                       ORDER BY sub.id DESC
                       LIMIT 1
    ) wn ON TRUE
