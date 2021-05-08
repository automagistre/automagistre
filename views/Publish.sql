SELECT p.entity_id AS id,
       p.published AS published,
       CONCAT_WS(
               ';',
               cb.id,
               CONCAT_WS(
                       ',',
                       cb_u.id,
                       cb_u.username,
                       COALESCE(cb_u.last_name, ''),
                       COALESCE(cb_u.first_name, '')
                   ),
               cb.created_at
           )       AS created
FROM (SELECT ROW_NUMBER() OVER (PARTITION BY entity_id ORDER BY id DESC) AS rownum, * FROM publish) p
         JOIN created_by cb ON cb.id = p.id
         JOIN users cb_u ON cb_u.id = cb.user_id
WHERE p.rownum = 1
