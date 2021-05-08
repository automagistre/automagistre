SELECT note.id,
       note.subject,
       note.text,
       note.type,
       CONCAT_WS(
               ';',
               cb.id,
               CONCAT_WS(
                       ',',
                       u.id,
                       u.username,
                       COALESCE(u.last_name, ''),
                       COALESCE(u.first_name, '')
                   ),
               cb.created_at
           ) AS created
FROM note
         JOIN created_by cb ON cb.id = note.id
         JOIN users u ON u.id = cb.user_id
