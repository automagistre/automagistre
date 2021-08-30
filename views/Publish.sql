SELECT p.entity_id AS id,
       p.tenant_id,
       p.published AS published,
       CONCAT_WS(
               ';',
               cb.id,
               CONCAT_WS(
                       ',',
                       cb.user_id,
                       'username',
                       'lastname',
                       'firstname'
                   ),
               cb.created_at
           )       AS created
FROM (SELECT ROW_NUMBER() OVER (PARTITION BY entity_id ORDER BY id DESC) AS rownum, * FROM publish) p
         JOIN created_by cb ON cb.id = p.id
WHERE p.rownum = 1
