SELECT p.entity_id   AS id,
       p.tenant_id,
       p.published   AS published,
       cb.user_id    AS created_by,
       cb.created_at AS created_at
FROM (SELECT ROW_NUMBER() OVER (PARTITION BY entity_id ORDER BY id DESC) AS rownum, * FROM publish) p
         JOIN created_by cb ON cb.id = p.id
WHERE p.rownum = 1
