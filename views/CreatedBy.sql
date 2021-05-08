SELECT cb.id,
       CONCAT_WS(
               ',',
               u.id,
               u.username,
               COALESCE(u.last_name, ''),
               COALESCE(u.first_name, '')
           )         AS by,
       cb.created_at AS at
FROM created_by cb
         JOIN users u ON u.id = cb.user_id
