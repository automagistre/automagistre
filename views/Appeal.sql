SELECT appeal.id,
       appeal.name,
       appeal.type,
       appeal.phone,
       appeal.email,
       COALESCE(status.status, 1) AS status,
       created_by.created_at
FROM (
         SELECT id, name, 1 AS type, phone, NULL AS email
         FROM appeal_calculator
         UNION ALL
         SELECT id, name, 2 AS type, phone, NULL AS email
         FROM appeal_cooperation
         UNION ALL
         SELECT id, name, 3 AS type, NULL AS phone, email
         FROM appeal_question
         UNION ALL
         SELECT id, name, 4 AS type, phone, NULL AS email
         FROM appeal_schedule
         UNION ALL
         SELECT id, name, 5 AS type, phone, NULL AS email
         FROM appeal_tire_fitting
         UNION ALL
         SELECT id, '', 6 AS type, phone, NULL AS email
         FROM appeal_call
     ) appeal
         LEFT JOIN LATERAL (SELECT *
                            FROM appeal_status sub
                            WHERE sub.appeal_id = appeal.id
                            ORDER BY sub.id DESC
                            LIMIT 1
    ) status ON TRUE
         JOIN created_by ON created_by.id = appeal.id
