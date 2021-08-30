SELECT note.id,
       note.tenant_id,
       note.subject,
       note.text,
       note.type,
       cb.created_at AS created_at,
       cb.user_id    AS created_by,
       db.created_at AS deleted_at,
       db.user_id    AS deleted_by
FROM note
         JOIN created_by cb ON cb.id = note.id
         LEFT JOIN note_delete ON note_delete.note_id = note.id
         LEFT JOIN created_by db ON db.id = note_delete.id
