SELECT note.id,
       note.tenant_id,
       note.subject,
       note.text,
       note.type,
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
           )   AS created,
       CASE
           WHEN db.id IS NOT NULL THEN
               CONCAT_WS(
                       ',',
                       db.user_id,
                       'username',
                       'lastname',
                       'firstname'
                   )
           END AS deleted_by
FROM note
         JOIN created_by cb ON cb.id = note.id
         LEFT JOIN note_delete ON note_delete.note_id = note.id
         LEFT JOIN created_by db ON db.id = note_delete.id
