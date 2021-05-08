SELECT i.id,
       cb.created_at,
       cbc.created_at AS closed_at
FROM inventorization i
         JOIN created_by cb ON cb.id = i.id
         LEFT JOIN inventorization_close ic ON i.id = ic.inventorization_id
         LEFT JOIN created_by cbc ON cbc.id = ic.id
