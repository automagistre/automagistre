SELECT es.id,
       es.employee_id,
       es.payday,
       es.amount,
       employee.person_id AS person_id,
       CONCAT_WS(
           ';',
            es_cb.id,
            CONCAT_WS(
                ',',
                es_cb_u.id,
                es_cb_u.username,
                COALESCE(es_cb_u.last_name, ''),
                COALESCE(es_cb_u.first_name, '')
            ),
            es_cb.created_at
        )      AS created,
       CASE
           WHEN ese.id IS NOT NULL
               THEN CONCAT_WS(
               ';',
                ese_cb.id,
                CONCAT_WS(
                    ',',
                    ese_cb_u.id,
                    ese_cb_u.username,
                    COALESCE(ese_cb_u.last_name, ''),
                    COALESCE(ese_cb_u.first_name, '')
                ),
                ese_cb.created_at
                )
           ELSE NULL
           END            AS ended
FROM employee_salary es
         JOIN created_by es_cb ON es_cb.id = es.id
         JOIN users es_cb_u ON es_cb_u.id = es_cb.user_id
         JOIN employee ON employee.id = es.employee_id
         LEFT JOIN employee_salary_end ese ON es.id = ese.salary_id
         LEFT JOIN created_by ese_cb ON ese_cb.id = ese.id
         LEFT JOIN users ese_cb_u ON ese_cb_u.id = ese_cb.user_id
