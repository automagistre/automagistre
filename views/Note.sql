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
           )   AS created,
       CASE
           WHEN db.id IS NOT NULL THEN
               CONCAT_WS(
                       ';',
                       db.id,
                       CONCAT_WS(
                               ',',
                               du.id,
                               du.username,
                               COALESCE(du.last_name, ''),
                               COALESCE(du.first_name, '')
                           ),
                       db.created_at
                   )
           END AS deleted_by
FROM note
         JOIN created_by cb ON cb.id = note.id
         JOIN users u ON u.id = cb.user_id
         LEFT JOIN note_delete ON note_delete.note_id = note.id
         LEFT JOIN created_by db ON db.id = note_delete.id
         LEFT JOIN users du ON du.id = db.user_id
