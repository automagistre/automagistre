SELECT o.id,
       CASE
           WHEN org IS NOT NULL
               THEN org.name
           ELSE p.lastname || ' ' || p.lastname
           END                                                           AS name,
       COALESCE(balance.money, 'RUB 0')                                  AS balance,
       o.email,
       CASE WHEN org IS NOT NULL THEN org.telephone ELSE p.telephone END AS telephone
FROM operand o
         LEFT JOIN organization org ON o.id = org.id
         LEFT JOIN person p ON o.id = p.id
         LEFT JOIN (
    SELECT o.id                                                    AS id,
           ct.amount_currency_code || ' ' || SUM(ct.amount_amount) AS money
    FROM operand o
             LEFT JOIN customer_transaction ct ON ct.operand_id = o.id
    GROUP BY o.id, ct.amount_currency_code
) balance ON balance.id = o.id
