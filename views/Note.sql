SELECT note.id,
       note.tenant_id,
       note.subject,
       note.text,
       note.type,
       cb.created_at AS created_at,
       cb.user_id    AS created_by
FROM note
         JOIN created_by cb ON cb.id = note.id
         LEFT JOIN note_delete ON note_delete.note_id = note.id
WHERE note_delete.id IS NULL
