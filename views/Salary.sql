SELECT es.id,
       es.tenant_id,
       es.employee_id,
       es.payday,
       es.amount,
       employee.person_id AS person_id,
       es_cb.user_id      AS created_by,
       es_cb.created_at   AS created_at,
       ese_cb.user_id     AS ended_by,
       ese_cb.created_at  AS ended_at
FROM employee_salary es
         JOIN created_by es_cb ON es_cb.id = es.id
         JOIN employee ON employee.id = es.employee_id
         LEFT JOIN employee_salary_end ese ON es.id = ese.salary_id
         LEFT JOIN created_by ese_cb ON ese_cb.id = ese.id
