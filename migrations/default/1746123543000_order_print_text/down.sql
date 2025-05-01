ALTER TABLE public.note
    DROP COLUMN is_public;

DROP VIEW public.note_view;
CREATE VIEW public.note_view(id, tenant_id, subject, text, type, created_at, created_by) AS
SELECT note.id,
       note.tenant_id,
       note.subject,
       note.text,
       note.type,
       note.created_at,
       note.created_by
FROM note
         LEFT JOIN note_delete ON note_delete.note_id = note.id
WHERE note_delete.id IS NULL;
