SELECT p.id,
       p.tenant_group_id,
       p.firstname,
       p.lastname,
       COALESCE(balance.money, 0) AS balance,
       p.email,
       p.telephone,
       p.office_phone,
       p.seller,
       p.contractor
FROM person p
         LEFT JOIN (
    SELECT ct.operand_id         AS id,
           SUM(ct.amount_amount) AS money
    FROM customer_transaction ct
    GROUP BY ct.operand_id, ct.amount_currency_code
) balance ON balance.id = p.id
