SELECT cb.id,
       cb.tenant_id,
       CONCAT_WS(
               ',',
               cb.user_id,
               'username',
               'lastname',
               'firstname'
           )         AS by,
       cb.created_at AS at
FROM created_by cb
