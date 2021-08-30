SELECT es.id,
       es.tenant_id,
       es.employee_id,
       es.payday,
       es.amount,
       employee.person_id AS person_id,
       CONCAT_WS(
           ';',
            es_cb.id,
            CONCAT_WS(
               ',',
               es_cb.user_id,
               'username',
               'lastname',
               'firstname'
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
                   ese_cb.user_id,
                   'username',
                   'lastname',
                   'firstname'
                ),
                ese_cb.created_at
                )
           ELSE NULL
           END            AS ended
FROM employee_salary es
         JOIN created_by es_cb ON es_cb.id = es.id
         JOIN employee ON employee.id = es.employee_id
         LEFT JOIN employee_salary_end ese ON es.id = ese.salary_id
         LEFT JOIN created_by ese_cb ON ese_cb.id = ese.id
