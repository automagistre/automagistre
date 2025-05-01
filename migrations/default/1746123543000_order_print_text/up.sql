ALTER TABLE public.note
    ADD COLUMN is_public bool DEFAULT FALSE;

DROP VIEW public.note_view;
CREATE VIEW public.note_view(id, tenant_id, subject, text, is_public, type, created_at, created_by) AS
SELECT note.id,
       note.tenant_id,
       note.subject,
       note.text,
       note.is_public,
       note.type,
       note.created_at,
       note.created_by
FROM note
         LEFT JOIN note_delete ON note_delete.note_id = note.id
WHERE note_delete.id IS NULL;
