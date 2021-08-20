SELECT o.id,
       o.tenant_id,
       o.name                           AS full_name,
       COALESCE(balance.money, 'RUB 0') AS balance,
       o.email,
       o.telephone,
       o.office_phone,
       o.seller,
       o.contractor,
       o.address,
       'organization'                   AS type
FROM organization o
         LEFT JOIN (
    SELECT ct.operand_id                                           AS id,
           ct.amount_currency_code || ' ' || SUM(ct.amount_amount) AS money
    FROM customer_transaction ct
    GROUP BY ct.operand_id, ct.amount_currency_code
) balance ON balance.id = o.id

UNION ALL

SELECT p.id,
       p.tenant_id,
       CONCAT_WS(' ', p.lastname, p.firstname) AS full_name,
       COALESCE(balance.money, 'RUB 0')        AS balance,
       p.email,
       p.telephone,
       p.office_phone,
       p.seller,
       p.contractor,
       NULL,
       'person'                                AS type
FROM person p
         LEFT JOIN (
    SELECT ct.operand_id                                           AS id,
           ct.amount_currency_code || ' ' || SUM(ct.amount_amount) AS money
    FROM customer_transaction ct
    GROUP BY ct.operand_id, ct.amount_currency_code
) balance ON balance.id = p.id
